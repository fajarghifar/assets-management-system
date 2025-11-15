<?php

namespace App\Services;

use App\Models\Borrowing;
use App\Models\BorrowingItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BorrowingReturnService
{
    public function returnItem(BorrowingItem $borrowingItem, int $quantity): void
    {
        $item = $borrowingItem->item;
        $borrowing = $borrowingItem->borrowing;

        if ($borrowing->status !== 'approved') {
            throw ValidationException::withMessages([
                'borrowing' => 'Peminjaman belum disetujui atau sudah selesai.',
            ]);
        }

        if ($item->type === 'fixed') {
            if ($quantity !== $borrowingItem->quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Barang tetap hanya bisa dikembalikan secara penuh.',
                ]);
            }
        } elseif ($item->type === 'consumable') {
            if ($quantity < 0 || $quantity + $borrowingItem->returned_quantity > $borrowingItem->quantity) {
                $max = $borrowingItem->quantity - $borrowingItem->returned_quantity;
                throw ValidationException::withMessages([
                    'quantity' => "Jumlah pengembalian tidak valid. Maksimal: {$max} unit.",
                ]);
            }
        }

        DB::transaction(function () use ($borrowingItem, $quantity, $item) {
            if ($item->type === 'consumable') {
                $borrowingItem->returned_quantity += $quantity;
                $borrowingItem->returned_at = now();
                $borrowingItem->save();
            } elseif ($item->type === 'fixed') {
                $instance = $borrowingItem->fixedInstance;
                $instance->status = 'available';
                $instance->save();

                $borrowingItem->returned_quantity = $quantity;
                $borrowingItem->returned_at = now();
                $borrowingItem->save();
            }

            $this->checkCompletion($borrowingItem->borrowing);
        });
    }

    protected function checkCompletion(Borrowing $borrowing): void
    {
        $freshBorrowing = $borrowing->fresh('items.item');

        $allReturned = $freshBorrowing->items->every(
            function (BorrowingItem $item) {
                if ($item->item->type === 'consumable') {
                    return true;
                }
                return $item->returned_quantity >= $item->quantity;
            }
        );

        if ($allReturned && $freshBorrowing->status !== 'completed') {
            $freshBorrowing->status = 'completed';
            $freshBorrowing->actual_return_date = now();
            $freshBorrowing->save();
        }
    }
}
