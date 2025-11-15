<?php

namespace App\Filament\Resources\Borrowings\Pages;

use App\Filament\Resources\Borrowings\BorrowingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBorrowing extends CreateRecord
{
    protected static string $resource = BorrowingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['code'] = 'BRW-' . now()->format('Y') . '-' . str_pad(BorrowingResource::getModel()::count() + 1, 3, '0', STR_PAD_LEFT);
        $data['status'] = 'pending';
        return $data;
    }
}
