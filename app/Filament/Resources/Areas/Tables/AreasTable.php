<?php

namespace App\Filament\Resources\Areas\Tables;

use App\Models\Area;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Collection;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;

class AreasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Daftar Area')
            ->columns([
                TextColumn::make('rowIndex')
                    ->label('No.')
                    ->rowIndex()
                    ->width('50px'),
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama Area')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'housing' => 'info',
                        'office' => 'success',
                        'store' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'housing' => 'Perumahan',
                        'office' => 'Kantor',
                        'store' => 'Store',
                        default => $state,
                    }),
                TextColumn::make('locations_count')
                    ->label('Jumlah Lokasi')
                    ->badge()
                    ->color(fn($state) => $state > 0 ? 'success' : 'gray')
                    ->formatStateUsing(fn($state) => $state > 0 ? "{$state} lokasi" : 'Belum ada lokasi'),
                TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(30),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah Area'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'housing' => 'Perumahan',
                        'office' => 'Kantor',
                        'store' => 'Store',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->iconSize('lg'),
                    EditAction::make()->iconSize('lg'),
                    DeleteAction::make()
                        ->iconSize('lg')
                        ->visible(fn(Area $record) => $record->locations_count === 0)
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Area?')
                        ->modalDescription('Area yang tidak memiliki lokasi bisa dihapus. Tindakan ini tidak bisa dibatalkan.')
                        ->modalSubmitActionLabel('Ya, Hapus')
                        ->action(function (Area $record) {
                            try {
                                $record->delete();
                                Notification::make()
                                    ->title('Area Dihapus')
                                    ->body("Area \"{$record->name}\" berhasil dihapus.")
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Gagal Menghapus Area')
                                    ->body('Pastikan area tidak digunakan di lokasi lain.')
                                    ->danger()
                                    ->send();
                            }
                        }),
                ])
                ->dropdownPlacement('left-start'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->deselectRecordsAfterCompletion(false)
                        ->action(function (Collection $records) {
                            $deleted = 0;
                            $errors = [];
                            foreach ($records as $record) {
                                if ($record->locations_count === 0) {
                                    try {
                                        $record->delete();
                                        $deleted++;
                                    } catch (\Exception $e) {
                                        $errors[] = "Gagal menghapus {$record->name}";
                                    }
                                } else {
                                    $errors[] = "Tidak bisa menghapus {$record->name}: masih memiliki lokasi.";
                                }
                            }
                            if ($deleted > 0) {
                                Notification::make()
                                    ->title("Berhasil menghapus {$deleted} area")
                                    ->success()
                                    ->send();
                            }
                            if (!empty($errors)) {
                                Notification::make()
                                    ->title('Beberapa area gagal dihapus')
                                    ->body(implode('\n', $errors))
                                    ->danger()
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->striped();
    }
}
