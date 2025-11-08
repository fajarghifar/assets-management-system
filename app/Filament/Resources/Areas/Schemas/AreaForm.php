<?php

namespace App\Filament\Resources\Areas\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AreaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Kode Area')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20)
                    ->helperText('Contoh: PH-A, OFF-B, STORE-C')
                    ->autofocus(),
                TextInput::make('name')
                    ->label('Nama Area')
                    ->required()
                    ->maxLength(100),
                Select::make('category')
                    ->label('Kategori')
                    ->options([
                        'housing' => 'Perumahan',
                        'office' => 'Kantor',
                        'store' => 'Store',
                    ])
                    ->required()
                    ->native(true),
                Textarea::make('address')
                    ->label('Alamat')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
