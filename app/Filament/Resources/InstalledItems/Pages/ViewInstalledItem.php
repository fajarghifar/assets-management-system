<?php

namespace App\Filament\Resources\InstalledItems\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\InstalledItems\InstalledItemResource;

class ViewInstalledItem extends ViewRecord
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
            EditAction::make(),
        ];
    }
}
