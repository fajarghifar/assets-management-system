<?php

namespace App\Filament\Resources\ItemStocks\Pages;

use App\Filament\Resources\ItemStocks\ItemStockResource;
use Filament\Resources\Pages\ManageRecords;

class ManageItemStocks extends ManageRecords
{
    protected static string $resource = ItemStockResource::class;

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
}
