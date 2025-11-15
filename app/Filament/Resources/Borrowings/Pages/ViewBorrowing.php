<?php

namespace App\Filament\Resources\Borrowings\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Borrowings\BorrowingResource;
use App\Filament\Resources\Borrowings\BorrowingActionsTrait;

class ViewBorrowing extends ViewRecord
{
    use BorrowingActionsTrait;

    protected static string $resource = BorrowingResource::class;

    protected function getHeaderActions(): array
    {
        // Ambil semua aksi dari Trait
        $borrowingActions = $this->getBorrowingHeaderActions($this->getRecord());

        // Tambahkan EditAction di depannya
        return [
            EditAction::make(),
            ...$borrowingActions,
        ];
    }
}
