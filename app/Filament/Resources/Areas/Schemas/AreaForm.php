<?php

namespace App\Filament\Resources\Areas\Schemas;

use App\Enums\AreaCategory;
use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

class AreaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Area Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Area')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(5)
                            ->dehydrateStateUsing(fn(string $state): string => Str::upper($state))
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('code', Str::upper($state));
                            })
                            ->helperText('Maksimal 5 Karakter (Contoh: OFF01, OFF02). Kode ini akan menjadi prefix untuk Lokasi.')
                            ->columnSpanFull(),
                        TextInput::make('name')
                            ->label('Nama Area')
                            ->required()
                            ->maxLength(100),
                        Select::make('category')
                            ->label('Kategori')
                            ->options(AreaCategory::class)
                            ->required()
                            ->native(false),
                        Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
