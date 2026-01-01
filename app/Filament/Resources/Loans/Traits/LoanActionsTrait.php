<?php

namespace App\Filament\Resources\Loans\Traits;

use App\Models\Loan;
use Filament\Actions\Action;
use App\Enums\LoanStatus;
use App\Enums\ProductType;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use App\Services\LoanReturnService;
use App\Services\LoanApprovalService;

trait LoanActionsTrait
{
    /**
     * Define the header actions available for Loan View/Edit pages.
     * Contains logic for Approve, Reject, and Return Items.
     */
    protected function getLoanHeaderActions(Model|Loan $record): array
    {
        $actions = [];

        // --- PENDING ACTION GROUP (Approve / Reject) ---
        if ($record->status === LoanStatus::Pending) {
            $actions[] = Action::make('approve')
                ->label('Setujui')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Setujui Peminjaman?')
                ->modalDescription('Stok barang akan dikurangi dan status aset akan berubah menjadi "Sedang Dipinjam".')
                ->action(function () use ($record) {
                    try {
                        app(LoanApprovalService::class)->approve($record);
                        Notification::make()->success()->title('Berhasil')->body('Peminjaman disetujui.')->send();
                        $this->redirect(request()->header('Referer'));
                    } catch (\Exception $e) {
                        Notification::make()->danger()->title('Gagal')->body($e->getMessage())->send();
                    }
                });

            $actions[] = Action::make('reject')
                ->label('Tolak')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->form([
                    Textarea::make('reason')
                        ->label('Alasan Penolakan')
                        ->required()
                        ->maxLength(255),
                ])
                ->action(function (array $data) use ($record) {
                    try {
                        app(LoanApprovalService::class)->reject($record, $data['reason']);
                        Notification::make()->success()->title('Berhasil')->body('Peminjaman ditolak.')->send();
                        $this->redirect(request()->header('Referer'));
                    } catch (\Exception $e) {
                        Notification::make()->danger()->body($e->getMessage())->send();
                    }
                });

            $actions[] = DeleteAction::make();
        }

        // --- ACTIVE LOAN ACTIONS (Return Items) ---
        if (in_array($record->status, [LoanStatus::Approved, LoanStatus::Overdue])) {
            $actions[] = Action::make('returnItems')
                ->label('Pengembalian Barang')
                ->icon('heroicon-o-arrow-left-start-on-rectangle')
                ->color('info')
                ->modalWidth('4xl')
                // Pre-fill form with items that haven't been fully returned
                ->fillForm(function (Loan $record): array {
                    $itemsToReturn = $record->loanItems()
                        ->whereRaw('quantity_borrowed > quantity_returned')
                        ->with(['consumableStock.product', 'asset.product', 'asset'])
                        ->get()
                        ->map(function ($item) {
                            $productName = $item->product_name;
                            $identity = $item->type === ProductType::Asset ? $item->asset?->code : 'Stok Habis Pakai';

                            return [
                                'loan_item_id' => $item->id,
                                'type' => $item->type->value,
                                'item_name' => $productName,
                                'identity_info' => $identity,
                                'remaining_qty' => $item->quantity_borrowed - $item->quantity_returned,
                                'is_returning' => false,
                                'return_quantity' => 1,
                            ];
                        })->values()->toArray();

                    return ['items_to_return' => $itemsToReturn];
                })
                ->form([
                    Section::make()
                        ->schema([
                            Repeater::make('items_to_return')
                                ->label('Daftar Barang yang Belum Kembali')
                                ->schema([
                                    Hidden::make('loan_item_id'),
                                    Hidden::make('type'),

                                    Grid::make(12)->schema([
                                        TextInput::make('item_name')
                                            ->label('Nama Barang')
                                            ->disabled()
                                            ->columnSpan(4),

                                        TextInput::make('identity_info')
                                            ->label('Info / Kode')
                                            ->disabled()
                                            ->columnSpan(3),

                                        TextInput::make('remaining_qty')
                                            ->label('Sisa')
                                            ->disabled()
                                            ->numeric()
                                            ->columnSpan(2),

                                        // Asset: Toggle for return
                                        Toggle::make('is_returning')
                                            ->label('Kembali?')
                                            ->onColor('success')
                                            ->visible(fn($get) => $get('type') === ProductType::Asset->value)
                                            ->columnSpan(3)
                                            ->inline(false),

                                        // Consumable: Quantity Input for partial return
                                        TextInput::make('return_quantity')
                                            ->label('Jumlah Kembali')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(fn($get) => (int) $get('remaining_qty'))
                                            ->visible(fn($get) => $get('type') === ProductType::Consumable->value)
                                            ->columnSpan(3),
                                    ]),
                                ])
                                ->addable(false)
                                ->deletable(false)
                                ->reorderable(false)
                        ])
                ])
                ->action(function (array $data) use ($record) {
                    $service = app(LoanReturnService::class);
                    $processed = 0;

                    foreach ($data['items_to_return'] ?? [] as $returnData) {
                        $loanItem = $record->loanItems->find($returnData['loan_item_id']);
                        if (!$loanItem) continue;

                        $qtyToReturn = 0;
                        if ($returnData['type'] === ProductType::Asset->value) {
                            if (!empty($returnData['is_returning'])) $qtyToReturn = 1;
                        } else {
                            $qtyToReturn = (int) ($returnData['return_quantity'] ?? 0);
                        }

                        if ($qtyToReturn > 0) {
                            $service->processReturn($record, $loanItem, $qtyToReturn);
                            $processed++;
                        }
                    }

                    if ($processed > 0) {
                         Notification::make()->success()->title('Berhasil')->body("$processed item dikembalikan.")->send();
                         $this->redirect(request()->header('Referer'));
                    } else {
                         Notification::make()->warning()->title('Dibatalkan')->body("Tidak ada item yang dipilih.")->send();
                    }
                });
        }

        return [
            ActionGroup::make($actions)
            ->hiddenLabel()
                ->color('primary')
                ->button(),
        ];
    }
}
