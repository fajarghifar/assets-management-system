<?php

namespace App\Filament\Resources\ItemStocks;

use App\Models\Item;
use App\Models\Location;
use App\Models\ItemStock;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Actions\ActionGroup;
use App\Services\ItemStockService;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ForceDeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ItemStocks\Pages\ManageItemStocks;

class ItemStockResource extends Resource
{
    protected static ?string $model = ItemStock::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('item_id')
                    ->label('Jenis Barang')
                    ->relationship(
                        name: 'item',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query
                            ->where('type', 'consumable')
                            ->orderBy('name')
                    )
                    ->getOptionLabelFromRecordUsing(fn(Item $record) => "{$record->name} ({$record->code})")
                    ->searchable(['name', 'code'])
                    ->required(),
                Select::make('location_id')
                    ->label('Lokasi')
                    ->relationship(
                        name: 'location',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query->orderBy('name')
                    )
                    ->getOptionLabelFromRecordUsing(fn(Location $record) => "{$record->name} ({$record->code})")
                    ->searchable(['name', 'code'])
                    ->required()
                    ->unique(ignoreRecord: true, modifyRuleUsing: fn($rule) => $rule->where('item_id', $this->getRecord()?->item_id)),
                TextInput::make('quantity')
                    ->label('Jumlah Stok')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                TextInput::make('min_quantity')
                    ->label('Stok Minimum')
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
                Textarea::make('notes')
                    ->label('Catatan')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Stok Barang Habis Pakai')
            ->columns([
                TextColumn::make('rowIndex')
                    ->label('No.')
                    ->rowIndex()
                    ->width('50px'),
                TextColumn::make('item.name')
                    ->label('Jenis Barang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('location.name')
                    ->label('Lokasi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label('Stok Tersedia')
                    ->sortable()
                    ->color(fn(int $state): string => $state === 0 ? 'danger' : ($state <= 10 ? 'warning' : 'success'))
                    ->badge(),
                TextColumn::make('min_quantity')
                    ->label('Stok Minimum')
                    ->sortable()
                    ->color(fn(int $state): string => $state > 0 ? 'info' : 'gray')
                    ->badge(),
                IconColumn::make('deleted_at')
                    ->label('Status Data')
                    ->state(fn($record) => !is_null($record->deleted_at))
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->trueIcon('heroicon-o-trash')
                    ->falseIcon('heroicon-o-check-circle')
                    ->tooltip(fn(ItemStock $record) => $record->deleted_at ? 'Dihapus' : 'Aktif'),
            ])->headerActions([
                    CreateAction::make()->label('Tambah Stok'),
                ])
            ->filters([
                SelectFilter::make('item')
                    ->label('Jenis Barang')
                    ->relationship(
                        name: 'item',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query
                            ->where('type', 'consumable')
                            ->orderBy('name')
                    )
                    ->multiple(),

                SelectFilter::make('location')
                    ->label('Lokasi')
                    ->relationship('location', 'name')
                    ->multiple(),

                TrashedFilter::make()
                    ->label('Status Data')
                    ->placeholder('Hanya data aktif')
                    ->trueLabel('Tampilkan semua data')
                    ->falseLabel('Hanya data yang dihapus'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->iconSize('lg'),
                    EditAction::make()->iconSize('lg'),
                    DeleteAction::make()
                        ->iconSize('lg')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Stok?')
                        ->modalDescription('Stok akan disembunyikan jika jumlahnya 0.')
                        ->action(function (ItemStock $record) {
                            try {
                                (new ItemStockService())->delete($record);
                                Notification::make()
                                    ->title('Berhasil')
                                    ->body("Stok {$record->item->name} di {$record->location->name} berhasil dihapus.")
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Gagal Menghapus')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    ForceDeleteAction::make()->iconSize('lg'),
                    RestoreAction::make()->iconSize('lg'),
                ])->dropdownPlacement('left-start'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageItemStocks::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['item', 'location'])
            ->withTrashed();
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
