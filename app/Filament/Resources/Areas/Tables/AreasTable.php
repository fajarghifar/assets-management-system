<?php

namespace App\Filament\Resources\Areas\Tables;

use App\Models\Area;
use Filament\Tables\Table;
use App\Enums\AreaCategory;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Validation\ValidationException;

class AreasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading('Daftar Area')
            ->columns([
                TextColumn::make('rowIndex')
                    ->label('No.')
                    ->rowIndex()
                    ->width('50px'),
                TextColumn::make('code')
                    ->label('Kode')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('name')
                    ->label('Nama Area')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->sortable(),
                TextColumn::make('locations_count')
                    ->label('Jml. Lokasi')
                    ->badge()
                    ->color(fn($state) => $state > 0 ? 'success' : 'gray')
                    ->sortable(),
                TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(30),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah Area'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options(AreaCategory::class),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->action(function (Area $record) {
                            try {
                                $record->delete();
                                Notification::make()->success()->title('Area berhasil dihapus')->send();
                            } catch (ValidationException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title('Gagal Menghapus')
                                    ->body($e->validator->errors()->first())
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->danger()
                                    ->title('Error')
                                    ->body($e->getMessage())
                                    ->send();
                            }
                        })
                ])
                ->dropdownPlacement('left-start'),
            ])
            ->toolbarActions([
                //
            ])
            ->striped();
    }
}
