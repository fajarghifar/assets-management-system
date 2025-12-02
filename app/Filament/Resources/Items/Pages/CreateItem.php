<?php

namespace App\Filament\Resources\Items\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Items\ItemResource;

class CreateItem extends CreateRecord
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
        ];
    }
}
