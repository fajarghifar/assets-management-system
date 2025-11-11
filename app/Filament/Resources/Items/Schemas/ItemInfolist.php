<?php

namespace App\Filament\Resources\Items\Schemas;

use App\Models\Item;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;

class ItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                // === Data Dasar Barang ===
                Section::make('Informasi Barang')
                    ->schema([
                        TextEntry::make('code'),
                        TextEntry::make('name'),
                        TextEntry::make('type')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'fixed' => 'success',
                                'consumable' => 'warning',
                                'installed' => 'info',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'fixed' => 'Barang Tetap',
                                'consumable' => 'Barang Habis Pakai',
                                'installed' => 'Barang Terpasang',
                                default => $state,
                            }),
                        TextEntry::make('description')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                // === Data Spesifik Berdasarkan Tipe ===
                // Untuk Consumable: Tampilkan Stok per Lokasi
                Section::make('Stok Barang Habis Pakai')
                    ->visible(fn(Item $record): bool => $record->type === 'consumable')
                    ->schema([
                        RepeatableEntry::make('stocks')
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make('Lokasi'),
                                TableColumn::make('Stok Tersedia'),
                                TableColumn::make('Stok Minimum'),
                            ])
                            ->schema([
                                TextEntry::make('location.name')
                                    ->placeholder('-'),
                                TextEntry::make('quantity')
                                    ->formatStateUsing(fn($state) => "{$state} unit"),
                                TextEntry::make('min_quantity')
                                    ->formatStateUsing(fn($state) => "{$state} unit"),
                            ])
                            ->placeholder('Tidak ada stok tercatat untuk barang ini.')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                // Untuk Fixed: Tampilkan Instance Barang Tetap
                Section::make('Instance Barang Tetap')
                    ->visible(fn(Item $record): bool => $record->type === 'fixed')
                    ->schema([
                        RepeatableEntry::make('fixedInstances')
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make('Kode Instance'),
                                TableColumn::make('Nomor Seri'),
                                TableColumn::make('Lokasi Saat Ini'),
                                TableColumn::make('Status'),
                            ])
                            ->schema([
                                TextEntry::make('code'),
                                TextEntry::make('serial_number')->placeholder('-'),
                                TextEntry::make('currentLocation.name')->placeholder('-'),
                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'available' => 'success',
                                        'borrowed' => 'warning',
                                        'maintenance' => 'danger',
                                        default => 'gray',
                                    }),
                            ])
                            ->placeholder('Belum ada instance untuk barang ini.')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                // Untuk Installed: Tampilkan Instance Barang Terpasang
                Section::make('Instance Barang Terpasang')
                    ->visible(fn(Item $record): bool => $record->type === 'installed')
                    ->schema([
                        RepeatableEntry::make('installedInstances')
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make('Kode Instance'),
                                TableColumn::make('Nomor Seri'),
                                TableColumn::make('Lokasi Pemasangan'),
                                TableColumn::make('Tanggal Pemasangan'),
                            ])
                            ->schema([
                                TextEntry::make('code'),
                                TextEntry::make('serial_number')->placeholder('-'),
                                TextEntry::make('installedLocation.name')->placeholder('-'),
                                TextEntry::make('installed_at')->date(),
                            ])
                            ->placeholder('Belum ada instance untuk barang ini.')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                // === Metadata Sistem ===
                Section::make('Metadata Sistem')
                    ->schema([
                        TextEntry::make('deleted_at')
                            ->label('Dihapus Pada')
                            ->dateTime()
                            ->visible(fn(Item $record): bool => $record->trashed()),
                        TextEntry::make('created_at')
                            ->label('Dibuat Pada')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label('Diperbarui Pada')
                            ->dateTime()
                            ->placeholder('-'),
                    ])
                    ->collapsible()
                    ->columns(2),
            ]);
    }
}
