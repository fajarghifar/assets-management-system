<?php

namespace App\Filament\Resources\Items\Schemas;

use App\Enums\ItemType;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Barang')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->columnSpanFull(),
                        TextInput::make('name')
                            ->label('Nama Barang')
                            ->required()
                            ->maxLength(100),
                        Select::make('type')
                            ->label('Jenis Barang')
                            ->options(ItemType::class)
                            ->required()
                            ->disabled(fn($record) => $record?->exists),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
            ]);
    }
}
