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

    protected function tableQuery(): Builder
    {
        return parent::tableQuery()
            ->withCount([
                'fixedInstances',
                'installedInstances',
            ])
            ->withSum('stocks', 'quantity');
    }
}
