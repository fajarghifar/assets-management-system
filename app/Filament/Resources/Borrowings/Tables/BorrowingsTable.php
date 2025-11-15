<?php

namespace App\Filament\Resources\Borrowings\Tables;

use App\Models\Borrowing;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use App\Services\BorrowingApprovalService;

class BorrowingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Daftar Peminjaman')
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Peminjam')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('purpose')
                    ->label('Tujuan')
                    ->limit(30),
                TextColumn::make('borrow_date')
                    ->label('Pinjam')
                    ->date()
                    ->sortable(),
                TextColumn::make('expected_return_date')
                    ->label('Kembali')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'completed' => 'info',
                        'overdue' => 'warning',
                    }),
            ])
            ->headerActions([
                CreateAction::make()->label('Ajukan Peminjaman'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'completed' => 'Selesai',
                        'overdue' => 'Terlambat',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->iconSize('lg'),
                    EditAction::make()->iconSize('lg'),
                    Action::make('approve')
                        ->label('Setujui')
                        ->color('success')
                        ->icon('heroicon-o-check-circle')
                        ->visible(fn(Borrowing $record) => $record->status === 'pending')
                        ->requiresConfirmation()
                        ->modalHeading('Setujui Peminjaman?')
                        ->modalDescription('Pastikan semua barang tersedia.')
                        ->action(function (Borrowing $record) {
                            try {
                                app(BorrowingApprovalService::class)->approve($record);
                                Notification::make()
                                    ->title('Berhasil')
                                    ->body('Peminjaman disetujui.')
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Gagal Menyetujui')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Action::make('reject')
                        ->label('Tolak')
                        ->color('danger')
                        ->icon('heroicon-o-x-circle')
                        ->visible(fn(Borrowing $record) => $record->status === 'pending')
                        ->requiresConfirmation()
                        ->modalHeading('Tolak Peminjaman?')
                        ->modalDescription('Peminjaman akan ditandai sebagai ditolak.')
                        ->action(function (Borrowing $record) {
                            $record->update(['status' => 'rejected']);
                            Notification::make()
                                ->title('Berhasil')
                                ->body('Peminjaman ditolak.')
                                ->success()
                                ->send();
                        }),
                    DeleteAction::make()
                        ->visible(fn(Borrowing $record) => $record->status === 'pending')
                        ->iconSize('lg'),
                ])
                    ->dropdownPlacement('left-start'),
            ]);
    }
}
