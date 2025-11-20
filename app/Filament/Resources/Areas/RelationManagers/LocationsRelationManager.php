<?php

namespace App\Filament\Resources\Areas\RelationManagers;

use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use Filament\Resources\RelationManagers\RelationManager;

class LocationsRelationManager extends RelationManager
{
    protected static string $relationship = 'locations';

    protected static ?string $title = 'Daftar Lokasi';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                ->schema([
                    TextInput::make('code')
                        ->label('Kode Lokasi')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(30)
                        ->helperText('Contoh: JMP1-RM1, BT-EVT'),
                    TextInput::make('name')
                        ->label('Nama Lokasi')
                        ->required()
                        ->maxLength(100),
                    Textarea::make('description')
                        ->label('Deskripsi')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('rowIndex')
                    ->label('#')
                    ->rowIndex(),
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('name')
                    ->label('Nama Lokasi')
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->wrap(),
            ])
            ->headerActions([
                    CreateAction::make()->label('Tambah Lokasi'),
                ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make()
                        ->label('Hapus')
                        ->modalHeading('Hapus Lokasi')
                        ->modalDescription('Apakah Anda yakin? Aksi ini tidak dapat dibatalkan.')
                        ->action(function (Model $record) {
                            try {
                                $record->delete();

                                Notification::make()
                                    ->success()
                                    ->title('Lokasi berhasil dihapus')
                                    ->send();

                            } catch (ValidationException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title('Penghapusan Ditolak')
                                    ->body($e->validator->errors()->first())
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->danger()
                                    ->title('Terjadi Kesalahan')
                                    ->body('Data tidak dapat dihapus karena kendala sistem.')
                                    ->send();
                            }
                        }),
                ])
                ->dropdownPlacement('left-start'),
            ])
            ->toolbarActions([
                //
            ]);
    }
}
