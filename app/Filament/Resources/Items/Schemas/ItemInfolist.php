<?php

namespace App\Filament\Resources\Items\Schemas;

use App\Enums\ItemType;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;

class ItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Barang')
                    ->schema([
                        TextEntry::make('code')
                            ->label('Kode SKU')
                            ->badge()
                            ->color('primary')
                            ->copyable()
                            ->weight(FontWeight::Bold)
                            ->columnSpanFull(),
                        TextEntry::make('name')
                            ->label('Nama Barang'),
                        TextEntry::make('type')
                            ->label('Tipe')
                            ->badge(),
                        TextEntry::make('description')
                            ->label('Deskripsi')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Statistik Inventaris')
                    ->schema([
                        TextEntry::make('total_units')
                            ->label('Total Stok / Unit')
                            ->state(function ($record) {
                                return match ($record->type) {
                                    ItemType::Consumable => $record->stocks()->sum('quantity') . ' pcs',
                                    ItemType::Fixed => $record->fixedInstances()->count() . ' unit',
                                    ItemType::Installed => $record->installedInstances()->count() . ' unit',
                                };
                            })
                            ->badge()
                            ->color(fn($state) => (int) $state > 0 ? 'success' : 'danger'),

                        TextEntry::make('created_at')
                            ->label('Terdaftar Sejak')
                            ->dateTime('d M Y'),
                    ])->columns(2),
            ]);
    }
}
