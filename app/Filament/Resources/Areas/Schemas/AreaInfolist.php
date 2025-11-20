<?php

namespace App\Filament\Resources\Areas\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;

class AreaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Area')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('code')
                            ->label('Kode')
                            ->badge()
                            ->copyable()
                            ->columnSpanFull(),
                        TextEntry::make('name')
                            ->label('Nama Area'),
                        TextEntry::make('category')
                            ->label('Kategori')
                            ->badge(),
                        TextEntry::make('address')
                            ->label(label: 'Alamat')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
