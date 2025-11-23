<?php

namespace App\Filament\Resources\Items\Tables;

use App\Models\Item;
use App\Enums\ItemType;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;

class ItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Daftar Barang')
            ->columns([
                TextColumn::make('rowIndex')
                    ->label('No.')
                    ->rowIndex()
                    ->width('30px'),
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('medium'),
                TextColumn::make('name')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('fixed_instances_count')
                    ->label('Unit Tetap')
                    ->counts('fixedInstances')
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('installed_instances_count')
                    ->label('Unit Terpasang')
                    ->counts('installedInstances')
                    ->toggleable()
                    ->sortable(),
                IconColumn::make('deleted_at')
                    ->label('Status')
                    ->state(fn($record) => !is_null($record->deleted_at))
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->trueIcon('heroicon-o-trash')
                    ->falseIcon('heroicon-o-check-circle')
                    ->tooltip(fn(Item $record) => $record->deleted_at ? 'Dihapus' : 'Aktif'),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah Barang'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(ItemType::class),
                TrashedFilter::make()
                    ->label('Status Data')
                    ->placeholder('Hanya data aktif')
                    ->trueLabel('Tampilkan semua data')
                    ->falseLabel('Hanya data yang dihapus'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->iconSize('lg'),
                    EditAction::make()->iconSize('lg'),
                    DeleteAction::make()->iconSize('lg'),
                    ForceDeleteAction::make()->iconSize('lg'),
                    RestoreAction::make()->iconSize('lg'),
                ])
                ->dropdownPlacement('left-start'),
            ])
            ->toolbarActions([
                //
            ])
            ->striped();
    }
}
