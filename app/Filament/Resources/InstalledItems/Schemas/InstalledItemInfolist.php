<?php

namespace App\Filament\Resources\InstalledItems\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;

class InstalledItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Aset')
                    ->schema([
                        TextEntry::make('code')
                            ->label('Kode Aset')
                            ->copyable()
                            ->weight('medium')
                            ->color('primary')
                            ->columnSpanFull(),
                        TextEntry::make('item.name')
                            ->label('Nama Barang'),
                        TextEntry::make('serial_number')
                            ->label('Nomor Seri')
                            ->copyable()
                            ->placeholder('-')
                            ->fontFamily('mono'),
                        TextEntry::make('location')
                            ->label('Lokasi Saat Ini')
                            ->formatStateUsing(
                                fn($record) =>
                                $record->location
                                ? $record->location->name . ' — ' . $record->location->area->name
                                : '-'
                            ),
                        TextEntry::make('installed_at')
                            ->label('Tanggal Pasang')
                            ->date('d F Y'),
                        TextEntry::make('notes')
                            ->label('Catatan')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
