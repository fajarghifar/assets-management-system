<?php

namespace App\Filament\Resources\Items\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Items\ItemResource;

class ViewItem extends ViewRecord
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    protected function getEloquentQuery(): Builder
    {
        // Panggil query dasar dari resource
        $query = parent::getEloquentQuery();

        // Tambahkan Eager Loading (with)
        $query->with([
            'stocks.location',
            'fixedInstances.currentLocation',
            'installedInstances.installedLocation'
        ]);

        return $query;
    }
}
