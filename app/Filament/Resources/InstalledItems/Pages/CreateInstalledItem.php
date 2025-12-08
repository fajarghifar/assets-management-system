<?php

namespace App\Filament\Resources\InstalledItems\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\InstalledItems\InstalledItemResource;

class CreateInstalledItem extends CreateRecord
{
    protected static string $resource = InstalledItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray')
                ->icon('heroicon-m-arrow-left'),
        ];
    }
}
