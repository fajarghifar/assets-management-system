<?php

namespace App\Filament\Resources\Items\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Items\ItemResource;

class ViewItem extends ViewRecord
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-m-arrow-left')
                ->url($this->getResource()::getUrl('index'))
                ->color('gray'),

            EditAction::make(),
        ];
    }
}
