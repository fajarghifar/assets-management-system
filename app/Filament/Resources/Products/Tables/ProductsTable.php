<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
use App\Enums\ProductType;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\QueryException;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Validation\ValidationException;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->withCount('assets')
                    ->withSum('consumableStocks as total_stock', 'quantity');
            })
            ->heading('Daftar Master Barang')
            ->columns([
                TextColumn::make('rowIndex')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->copyable()
                    ->weight('medium')
                    ->color('primary'),
                TextColumn::make('name')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('type')
                    ->label('Jenis Barang')
                    ->sortable()
                    ->badge(),
                TextColumn::make('stock_display')
                    ->label('Total Stok')
                    ->state(function (Product $record) {
                        if ($record->type === ProductType::Asset) {
                            return ($record->assets_count ?? 0) . ' Unit';
                        }
                        return ($record->total_stock ?? 0) . ' Pcs';
                    })
                    ->badge()
                    ->color(fn($state) => (int) $state > 0 ? 'success' : 'danger')
                    ->alignCenter(),
                IconColumn::make('can_be_loaned')
                    ->label('Status Pinjam?')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable()
                    ->tooltip(fn($state) => $state ? 'Bisa Dipinjam' : 'Non-Peminjaman'),
                TextColumn::make('created_at')
                ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah Barang'),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->optionsLimit(20),
                SelectFilter::make('type')
                    ->label('Tipe Produk')
                    ->options(ProductType::class)
                    ->native(false),
                TernaryFilter::make('can_be_loaned')
                    ->label('Status Peminjaman')
                    ->native(false)
                    ->placeholder('Semua Barang')
                    ->trueLabel('Bisa Dipinjam')
                    ->falseLabel('Non-Peminjaman'),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make()
                        ->modalHeading('Hapus Produk')
                        ->modalDescription('Apakah Anda yakin? Data akan dihapus PERMANEN dan tidak dapat dikembalikan.')
                        ->action(function (Product $record) {
                            try {
                                $record->delete();
                                Notification::make()
                                    ->success()
                                    ->title('Produk berhasil dihapus')
                                    ->send();
                            } catch (ValidationException $e) {
                                    Notification::make()
                                        ->danger()
                                        ->title('Gagal Menghapus')
                                        ->body($e->validator->errors()->first())
                                        ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->danger()
                                    ->title('Error Sistem')
                                    ->body($e->getMessage())
                                    ->send();
                            }
                        }),
                ])->dropdownPlacement('left-start'),
            ])
            ->toolbarActions([
                //
            ]);
    }
}
