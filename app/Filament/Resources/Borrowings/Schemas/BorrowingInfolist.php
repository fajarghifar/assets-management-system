<?php

namespace App\Filament\Resources\Borrowings\Schemas;

use App\Models\Borrowing;
use Filament\Schemas\Schema;
use App\Models\BorrowingItem;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;

class BorrowingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Peminjaman')
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('code')
                                ->label('Kode Peminjaman')
                                ->icon('heroicon-o-hashtag'),

                            TextEntry::make('user.name')
                                ->label('Peminjam')
                                ->icon('heroicon-o-user'),

                            TextEntry::make('status')
                                ->label('Status')
                                ->badge()
                                ->color(fn(string $state): string => match ($state) {
                                    'pending' => 'gray',
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    'completed' => 'info',
                                    'overdue' => 'warning',
                                    default => 'gray',
                                })
                                ->formatStateUsing(fn(string $state): string => match ($state) {
                                    'pending' => 'Pending',
                                    'approved' => 'Disetujui',
                                    'rejected' => 'Ditolak',
                                    'completed' => 'Selesai',
                                    'overdue' => 'Terlambat',
                                    default => ucfirst($state),
                                }),
                        ]),
                        Grid::make(3)->schema([
                            TextEntry::make('borrow_date')
                                ->label('Tanggal Pinjam')
                                ->dateTime('d M Y, H:i')
                                ->icon('heroicon-o-calendar-days'),
                            TextEntry::make('expected_return_date')
                                ->label('Wajib Kembali')
                                ->dateTime('d M Y, H:i')
                                ->icon('heroicon-o-calendar'),
                            TextEntry::make('actual_return_date')
                                ->label('Aktual Kembali')
                                ->dateTime('d M Y, H:i')
                                ->icon('heroicon-o-check-circle')
                                ->placeholder('-'),
                        ]),
                        TextEntry::make('purpose')
                            ->label('Tujuan Peminjaman')
                            ->columnSpanFull(),
                        TextEntry::make('notes')
                            ->label('Catatan')
                            ->placeholder('-')
                            ->columnSpanFull(),

                        RepeatableEntry::make('items')
                            ->hiddenLabel()
                            ->table(
                                [
                                    TableColumn::make('Barang')->width('30%'),
                                    TableColumn::make('Tipe'),
                                    TableColumn::make('Jml. Pinjam')->alignRight(),
                                    TableColumn::make('Jml. Kembali')->alignRight(),
                                ]
                            )
                            ->schema([
                                TextEntry::make('item.name')
                                    ->weight('medium'),

                                TextEntry::make('item.type')
                                    ->label('Tipe')
                                    ->badge()
                                    ->formatStateUsing(fn($state) => match ($state) {
                                        'fixed' => 'Barang Tetap',
                                        'consumable' => 'Habis Pakai',
                                        default => $state
                                    })
                                    ->color(fn($state) => match ($state) {
                                        'fixed' => 'primary',
                                        'consumable' => 'success',
                                        default => 'gray'
                                    }),

                                TextEntry::make('quantity')
                                    ->label('Jml. Pinjam')
                                    ->suffix(' Unit')
                                    ->alignRight(),

                                TextEntry::make('returned_quantity')
                                    ->label('Jml. Kembali')
                                    ->suffix(' Unit')
                                    ->alignRight(),
                            ])
                            ->columnSpanFull()
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
