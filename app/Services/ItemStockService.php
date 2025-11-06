<?php

namespace App\Services;

use App\Models\ItemStock;
use Illuminate\Validation\ValidationException;

class ItemStockService
{
    /**
     * Validate the stock record before creating or updating.
     */
    public function validate(ItemStock $stock): void
    {
        // Ensure the item type is 'consumable'
        if ($stock->item->type !== 'consumable') {
            throw ValidationException::withMessages([
                'item_id' => 'Hanya Barang Habis Pakai yang bisa memiliki stok.',
            ]);
        }

        // Ensure quantity is not negative
        if ($stock->quantity < 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Stok tidak boleh negatif.',
            ]);
        }

        // Ensure minimum quantity is logical (not negative)
        if ($stock->min_quantity < 0) {
            throw ValidationException::withMessages([
                'min_quantity' => 'Stok minimum tidak boleh negatif.',
            ]);
        }
    }

    /**
     * Decrease the stock quantity (e.g., when lending or using items).
     */
    public function decrease(ItemStock $stock, int $quantity): void
    {
        // Check if there is sufficient stock to decrease
        if ($stock->quantity < $quantity) {
            throw ValidationException::withMessages([
                'quantity' => "Stok tidak mencukupi. Tersedia: {$stock->quantity}, Diminta: {$quantity}.",
            ]);
        }

        $stock->quantity -= $quantity;
        $stock->save();
    }

    /**
     * Increase the stock quantity (e.g., when returning or restocking items).
     */
    public function increase(ItemStock $stock, int $quantity): void
    {
        // Ensure the added quantity is positive
        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Jumlah penambahan harus lebih dari 0.',
            ]);
        }

        $stock->quantity += $quantity;
        $stock->save();
    }

    /**
     * Soft delete the stock record.
     */
    public function delete(ItemStock $stock): void
    {
        // Allow deletion only if stock quantity is zero
        if ($stock->quantity > 0) {
            throw ValidationException::withMessages([
                'stock' => 'Tidak bisa menghapus: masih memiliki stok.',
            ]);
        }

        $stock->delete();
    }

    /**
     * Restore a previously soft-deleted stock record.
     */
    public function restore(ItemStock $stock): void
    {
        $stock->restore();
    }

    /**
     * Permanently delete the stock record.
     */
    public function forceDelete(ItemStock $stock): void
    {
        $stock->forceDelete();
    }
}
