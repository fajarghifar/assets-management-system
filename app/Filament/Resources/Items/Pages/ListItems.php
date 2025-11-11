<?php

namespace App\Filament\Resources\Items\Pages;

use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Items\ItemResource;

class ListItems extends ListRecords
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    public function getHeading(): string
    {
        return '';
    }

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();

        $query->withCount([
            'fixedInstances',
            'installedInstances',
        ]);

        return $query;
    }
}
