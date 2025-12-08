<?php

namespace App\Filament\Resources\InstalledItems;

use Filament\Tables\Table;
use Filament\Schemas\Schema;
use App\Models\InstalledItem;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\InstalledItems\Pages\EditInstalledItem;
use App\Filament\Resources\InstalledItems\Pages\ViewInstalledItem;
use App\Filament\Resources\InstalledItems\Pages\ListInstalledItems;
use App\Filament\Resources\InstalledItems\Pages\CreateInstalledItem;
use App\Filament\Resources\InstalledItems\Schemas\InstalledItemForm;
use App\Filament\Resources\InstalledItems\Tables\InstalledItemsTable;
use App\Filament\Resources\InstalledItems\Schemas\InstalledItemInfolist;
use App\Filament\Resources\InstalledItems\RelationManagers\HistoriesRelationManager;

class InstalledItemResource extends Resource
{
    protected static ?string $model = InstalledItem::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return InstalledItemForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return InstalledItemInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return InstalledItemsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            HistoriesRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => ListInstalledItems::route('/'),
            'create' => CreateInstalledItem::route('/create'),
            'view' => ViewInstalledItem::route('/{record}'),
            'edit' => EditInstalledItem::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['item', 'location'])
            ->withTrashed();
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
