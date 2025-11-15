<?php

namespace App\Filament\Resources\Borrowings;

use App\Models\Borrowing;
use Filament\Actions\Action;
use App\Models\BorrowingItem;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Model;
use App\Services\BorrowingReturnService;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Services\BorrowingApprovalService;
use Filament\Schemas\Components\Utilities\Get;

trait BorrowingActionsTrait
{
    protected function getBorrowingHeaderActions(Model $record): array
    {
        $actions = [];

        $actions[] = DeleteAction::make()
            ->visible(fn() => $record->status === 'pending');

        if ($record->status === 'pending') {
            $actions[] = Action::make('approve')
                ->label('Setujui Peminjaman')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->modalHeading('Setujui Peminjaman?')
                ->action(function () use ($record) {
                    try {
                        app(BorrowingApprovalService::class)->approve($record);
                        Notification::make()->success()->title('Berhasil')->body('Peminjaman disetujui.')->send();
                        $this->redirect($this->getUrl(['record' => $this->getRecord()]));
                    } catch (\Exception $e) {
                        Notification::make()->danger()->title('Gagal')->body($e->getMessage())->send();
                    }
                });

            $actions[] = Action::make('reject')
                ->label('Tolak Peminjaman')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->modalHeading('Tolak Peminjaman?')
                ->action(function () use ($record) {
                    $record->update(['status' => 'rejected']);
                    Notification::make()->success()->title('Berhasil')->body('Peminjaman ditolak.')->send();
                    $this->redirect($this->getUrl(['record' => $this->getRecord()]));
                });
        }

        if ($record->status === 'approved') {
            $actions[] = Action::make('returnItems')
                ->label('Kembalikan Barang')
                ->icon('heroicon-o-archive-box-arrow-down')
                ->color('info')
                ->modalHeading('Form Pengembalian Barang')
                ->modalWidth('4xl')
                ->fillForm(function (Borrowing $record): array {
                    $itemsToReturn = $record->items()
                        ->whereRaw('quantity > returned_quantity')
                        ->with('item')
                        ->get()
                        ->map(function (BorrowingItem $item) {
                            return [
                                'borrowing_item_id' => $item->id,
                                'item_type' => $item->item->type,
                                'item_name' => $item->item->name,
                                'total_borrowed' => $item->quantity,
                                'total_returned' => $item->returned_quantity,
                                'return_fixed' => false,
                                'return_quantity' => 0,
                            ];
                        })->values();

                    return ['items_to_return' => $itemsToReturn];
                })
                ->schema([
                    Repeater::make('items_to_return')
                        ->label('Barang Belum Kembali')
                        ->schema([
                            Hidden::make('borrowing_item_id'),
                            Hidden::make('item_type'),
                            TextInput::make('item_name')->label('Barang')->disabled()->columnSpan(3),
                            TextInput::make('total_borrowed')->label('Total Pinjam')->disabled(),
                            TextInput::make('total_returned')->label('Sudah Kembali')->disabled(),
                            Toggle::make('return_fixed')->label('Kembalikan?')
                                ->visible(fn(Get $get) => $get('item_type') === 'fixed')
                                ->columnSpan(2),
                            TextInput::make('return_quantity')->label('Jumlah Kembali')
                                ->numeric()->minValue(0)->default(0)
                                ->maxValue(fn(Get $get) => $get('total_borrowed') - $get('total_returned'))
                                ->visible(fn(Get $get) => $get('item_type') === 'consumable')
                                ->columnSpan(2),
                        ])
                        ->columns(7)
                        ->addable(false)
                        ->deletable(false)
                        ->itemLabel(fn($state) => $state['item_name'] ?? 'Barang'),
                ])
                ->action(function ($data) use ($record) {
                    $service = app(BorrowingReturnService::class);
                    $errors = [];

                    foreach ($data['items_to_return'] ?? [] as $returnData) {
                        $borrowingItem = $record->items->find($returnData['borrowing_item_id']);
                        if (!$borrowingItem)
                            continue;

                        $quantityToReturn = 0;
                        $processThisItem = false; // Flag untuk memproses

                        if ($returnData['item_type'] === 'fixed') {
                            if ($returnData['return_fixed'] === true) {
                                $quantityToReturn = $borrowingItem->quantity;
                                $processThisItem = true;
                            }
                        } else { // consumable
                            $quantityToReturn = (int) ($returnData['return_quantity'] ?? 0);
                            $processThisItem = true; // Selalu proses consumable, walau 0
                        }

                        // PERBAIKAN: Gunakan flag $processThisItem
                        if ($processThisItem) {
                            try {
                                $service->returnItem($borrowingItem, $quantityToReturn);
                            } catch (\Exception $e) {
                                $errors[] = "{$borrowingItem->item->name}: " . $e->getMessage();
                            }
                        }
                    }

                    if (empty($errors)) {
                        Notification::make()->success()->title('Berhasil')->body('Pengembalian barang diproses.')->send();
                    } else {
                        Notification::make()->danger()->title('Terjadi Kesalahan')->body(implode("\n", $errors))->send();
                    }

                    $this->redirect($this->getUrl(['record' => $this->getRecord()]));
                });
        }

        return $actions;
    }
}
