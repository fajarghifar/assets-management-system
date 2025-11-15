<?php
namespace App\Services;

use App\Models\Item;
use App\Models\Borrowing;
use App\Models\ItemStock;
use App\Models\BorrowingItem;
use App\Models\FixedItemInstance;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BorrowingService
{
    /**
     * Tambahkan detail barang ke peminjaman (simpan ke borrowing_items).
     */
    public function addItem(array $data): BorrowingItem
    {
        $item = Item::withTrashed()->findOrFail($data['item_id']);

        if (!in_array($item->type, ['consumable', 'fixed'])) {
            throw ValidationException::withMessages([
                'item_id' => 'Barang ini tidak dapat dipinjam.',
            ]);
        }

        return DB::transaction(function () use ($data, $item) {
            if ($item->type === 'consumable') {
                return $this->handleConsumable($data, $item);
            } else {
                return $this->handleFixed($data, $item);
            }
        });
    }

    private function handleConsumable(array $data, Item $item): BorrowingItem
    {
        $locationId = $data['location_id'] ?? null;
        $quantity = $data['quantity'] ?? 1;

        if (!$locationId) {
            throw ValidationException::withMessages([
                'location_id' => 'Lokasi asal stok wajib diisi untuk barang habis pakai.',
            ]);
        }
        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Jumlah harus lebih dari 0.',
            ]);
        }

        // ⚠️ Hanya validasi dasar — stok akan diverifikasi saat approve
        $stock = ItemStock::where('item_id', $item->id)
            ->where('location_id', $locationId)
            ->first();

        if ($stock && $stock->quantity < $quantity) {
            throw ValidationException::withMessages([
                'quantity' => "Stok tidak mencukupi di lokasi ini.",
            ]);
        }

        return BorrowingItem::create([
            'borrowing_id' => $data['borrowing_id'],
            'item_id' => $item->id,
            'quantity' => $quantity,
            'location_id' => $locationId,
        ]);
    }

    private function handleFixed(array $data, Item $item): BorrowingItem
    {
        $instanceId = $data['fixed_instance_id'] ?? null;

        if (!$instanceId) {
            throw ValidationException::withMessages([
                'fixed_instance_id' => 'Instance barang tetap wajib dipilih.',
            ]);
        }

        $instance = FixedItemInstance::findOrFail($instanceId);
        if ($instance->item_id !== $item->id) {
            throw ValidationException::withMessages([
                'fixed_instance_id' => 'Instance tidak sesuai dengan jenis barang.',
            ]);
        }
        if ($instance->status !== 'available') {
            throw ValidationException::withMessages([
                'fixed_instance_id' => "Instance ini sedang {$instance->status}.",
            ]);
        }

        return BorrowingItem::create([
            'borrowing_id' => $data['borrowing_id'],
            'item_id' => $item->id,
            'fixed_instance_id' => $instance->id,
            'quantity' => 1,
        ]);
    }
}
