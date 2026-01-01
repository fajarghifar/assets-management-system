<?php

namespace App\Filament\Resources\Loans\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Loans\LoanResource;

class ListLoans extends ListRecords
{
    protected static string $resource = LoanResource::class;


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
