<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Enums\ProductType;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;

class ProductForm
{
    /**
     * Configure the form schema for the Product resource.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('resources.products.fields.product_info'))
                    ->description(__('resources.products.fields.product_info_desc'))
                    ->schema([
                        TextInput::make('code')
                            ->label(__('resources.products.fields.code'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20)
                            ->regex('/^[A-Z0-9]+(?:-[A-Z0-9]+)*$/')
                            ->placeholder(__('resources.products.fields.code_placeholder'))
                            ->helperText(__('resources.products.fields.code_helper'))
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, callable $set) => $set('code', strtoupper($state)))
                            ->disabled(fn($operation) => $operation === 'edit')
                            ->dehydrated(),

                        TextInput::make('name')
                            ->label(__('resources.products.fields.name'))
                            ->required()
                            ->maxLength(100)
                            ->placeholder(__('resources.products.fields.description_placeholder')),

                        Select::make('category_id')
                            ->label(__('resources.products.fields.category'))
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->optionsLimit(20)
                            ->required()
                            ->createOptionForm(null),

                        Select::make('type')
                            ->label(__('resources.products.fields.type'))
                            ->options(ProductType::class)
                            ->required()
                            ->native(false)
                            ->disabled(fn($operation) => $operation === 'edit')
                            ->dehydrated(),

                        Toggle::make('can_be_loaned')
                            ->label(__('resources.products.fields.can_be_loaned'))
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(true)
                            ->helperText(__('resources.products.fields.loanable_helper'))
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label(__('resources.products.fields.description'))
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder(__('resources.products.fields.description_placeholder')),
                ])
                ->columns(2)
                ->columnSpanFull(),
            ]);
    }
}
