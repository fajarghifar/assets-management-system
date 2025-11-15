<?php
namespace App\Services;

use App\Models\Borrowing;
use App\Models\BorrowingItem;
use App\Models\ItemStock;
use App\Models\FixedItemInstance;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BorrowingApprovalService
{
    public function approve(Borrowing $borrowing): void
    {
        if ($borrowing->status !== 'pending') {
            throw ValidationException::withMessages([
                'borrowing' => 'Hanya peminjaman pending yang bisa disetujui.',
            ]);
        }

        DB::transaction(function () use ($borrowing) {
            foreach ($borrowing->items as $item) {
                $this->processItem($item);
            }
            $borrowing->status = 'approved';
            $borrowing->save();
        });
    }

    private function processItem(BorrowingItem $borrowingItem): void
    {
        $item = $borrowingItem->item;

        if ($item->type === 'consumable') {
            $this->processConsumableBorrow($borrowingItem);
        } elseif ($item->type === 'fixed') {
            $this->processFixedBorrow($borrowingItem);
        }
    }

    private function processConsumableBorrow(BorrowingItem $borrowingItem): void
    {
        $stock = ItemStock::where('item_id', $borrowingItem->item_id)
            ->where('location_id', $borrowingItem->location_id)
            ->first();

        if (!$stock || $stock->quantity < $borrowingItem->quantity) {
            $available = $stock ? $stock->quantity : 0;
            throw ValidationException::withMessages([
                'item' => "Stok tidak mencukupi. Tersedia: {$available}, Diminta: {$borrowingItem->quantity}.",
            ]);
        }

        // Kurangi stok
        $stock->quantity -= $borrowingItem->quantity;
        $stock->save();
    }

    private function processFixedBorrow(BorrowingItem $borrowingItem): void
    {
        $instance = $borrowingItem->fixedInstance;

        if (!$instance || $instance->status !== 'available') {
            throw ValidationException::withMessages([
                'item' => 'Instance ini tidak tersedia untuk dipinjam.',
            ]);
        }

        // Ubah status → borrowed
        $instance->status = 'borrowed';
        // 🔑 Lokasi TETAP → tidak diubah!
        $instance->save();
    }
}
