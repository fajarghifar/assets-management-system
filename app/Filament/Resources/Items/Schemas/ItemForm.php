<?php

namespace App\Filament\Resources\Items\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label('Jenis Barang')
                    ->options([
                        'fixed' => 'Barang Tetap',
                        'consumable' => 'Barang Habis Pakai',
                        'installed' => 'Barang Terpasang',
                    ])
                    ->required()
                    ->disabled(fn($record) => $record?->exists),
                TextInput::make('code')
                    ->label('Kode Barang')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->autofocus(),
                TextInput::make('name')
                    ->label('Nama Barang')
                    ->required()
                    ->maxLength(100),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }
}
