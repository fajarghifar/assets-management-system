<?php

namespace App\Filament\Resources\Borrowings\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Borrowings\BorrowingResource;
use App\Filament\Resources\Borrowings\BorrowingActionsTrait;

class EditBorrowing extends EditRecord
{
    use BorrowingActionsTrait;

    protected static string $resource = BorrowingResource::class;

    protected function getHeaderActions(): array
    {
        return $this->getBorrowingHeaderActions($this->getRecord());
    }
}
