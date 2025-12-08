<?php

namespace App\Filament\Resources\InstalledItems\Pages;

use App\Filament\Resources\InstalledItems\InstalledItemResource;
use Filament\Resources\Pages\ListRecords;

class ListInstalledItems extends ListRecords
{
    protected static string $resource = InstalledItemResource::class;

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
