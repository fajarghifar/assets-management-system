<?php

namespace App\Filament\Resources\Borrowings\Schemas;

use App\Models\Item;
use App\Models\Borrowing;
use App\Models\ItemStock;
use Filament\Actions\Action;
use Filament\Schemas\Schema;
use App\Models\FixedItemInstance;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Facades\Cache;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Utilities\Get;

class BorrowingForm
{
    public static function configure(Schema $schema): Schema
    {
        $isEditable = fn (?Borrowing $record) => !$record || $record->status === 'pending';

        return $schema
            ->components([
                Section::make('Informasi Peminjaman')
                    ->schema([
                        Select::make('user_id')
                            ->label('Peminjam')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required()
                            ->disabled(fn(?Borrowing $record) => !$isEditable($record)),

                        DateTimePicker::make('borrow_date')
                            ->label('Tanggal Pinjam')
                            ->required()
                            ->native(false)
                            ->default(now())
                            ->disabled(fn(?Borrowing $record) => !$isEditable($record)),

                        DateTimePicker::make('expected_return_date')
                            ->label('Tanggal Rencana Kembali')
                            ->required()
                            ->native(false)
                            ->disabled(fn(?Borrowing $record) => !$isEditable($record)),

                        Textarea::make('purpose')
                            ->label('Tujuan Peminjaman')
                            ->rows(2)
                            ->required()
                            ->columnSpanFull()
                            ->disabled(fn(?Borrowing $record) => !$isEditable($record)),

                        Textarea::make('notes')
                            ->label('Catatan (Opsional)')
                            ->rows(2)
                            ->columnSpanFull()
                            ->disabled(fn(?Borrowing $record) => !$isEditable($record)),
                    ])
                    ->columns(2),

                Section::make('Barang yang Dipinjam')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Grid::make(4)->schema([
                                    Select::make('item_id')
                                        ->label('Barang')
                                        ->options(function () {
                                            return Cache::remember('borrowable_items_list', 60, function () {
                                                return Item::whereIn('type', ['fixed', 'consumable'])
                                                    ->orderBy('name')
                                                    ->get()
                                                    ->mapWithKeys(fn($item) => [
                                                        $item->id => "{$item->name} ({$item->code}) – " . match ($item->type) {
                                                            'fixed' => 'Barang Tetap',
                                                            'consumable' => 'Habis Pakai',
                                                            default => '–',
                                                        }
                                                    ]);
                                            });
                                        })
                                        ->searchable()
                                        ->live(onBlur: true)
                                        ->required()
                                        ->columnSpanFull(),

                                    Select::make('fixed_instance_id')
                                        ->label('Instance Barang')
                                        ->options(function (Get $get) {
                                            $itemId = $get('item_id');
                                            if (!$itemId)
                                                return [];

                                            return FixedItemInstance::with('location')
                                                ->where('item_id', $itemId)
                                                ->where('status', 'available')
                                                ->get()
                                                // PERBAIKAN: Atasi error '??' di dalam string
                                                ->mapWithKeys(function ($inst) {
                                                    $locationName = $inst->location?->name ?? 'N/A';
                                                    return [$inst->id => "{$inst->code} (Lokasi: {$locationName})"];
                                                });
                                        })
                                        ->searchable()
                                        ->required()
                                        ->columnSpan(4)
                                        ->visible(fn(Get $get) => $get('item_id') && Item::find($get('item_id'))?->type === 'fixed'),

                                    Select::make('location_id')
                                        ->label('Lokasi Stok')
                                        ->options(function (Get $get) {
                                            $itemId = $get('item_id');
                                            if (!$itemId)
                                                return [];

                                            return ItemStock::with('location')
                                                ->where('item_id', $itemId)
                                                ->where('quantity', '>', 0)
                                                ->get()
                                                ->mapWithKeys(fn($stock) => [$stock->location_id => "{$stock->location->name} (Stok: {$stock->quantity})"]);
                                        })
                                        ->searchable()
                                        ->required()
                                        ->columnSpan(3)
                                        ->visible(fn(Get $get) => $get('item_id') && Item::find($get('item_id'))?->type === 'consumable'),

                                    TextInput::make('quantity')
                                        ->label('Jumlah')
                                        ->numeric()
                                        ->minValue(1)
                                        ->default(1)
                                        ->disabled(fn(Get $get) => $get('item_id') && Item::find($get('item_id'))?->type === 'fixed')
                                        ->required()
                                        ->columnSpan(1),
                                ])
                            ])
                            ->addActionLabel('Tambah Barang')
                            ->itemLabel(fn($state) => $state['item_id'] ? Item::find($state['item_id'])?->name : 'Barang Baru')
                            ->columns(1)
                            ->disabled(fn(?Borrowing $record) => !$isEditable($record))
                            ->minItems(1)
                            // PERBAIKAN: Logika disabled() yang benar untuk actions
                            ->deleteAction(
                                fn(Action $action) => $action
                                    ->disabled(fn(?Borrowing $record) => !$isEditable($record))
                            )
                            ->addAction(
                                fn(Action $action) => $action
                                    ->disabled(fn(?Borrowing $record) => !$isEditable($record))
                            ),
                    ]),
            ]);
    }
}
