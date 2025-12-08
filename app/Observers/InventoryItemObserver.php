<?php

namespace App\Observers;

use App\Models\Item;
use App\Enums\ItemType;
use Illuminate\Support\Str;
use App\Models\InventoryItem;
use App\Enums\InventoryStatus;
use Illuminate\Validation\ValidationException;

class InventoryItemObserver
{
    /**
     * Handle the InventoryItem "creating" event.
     * Fokus: Generate Kode & Default Value
     */
    public function creating(InventoryItem $inventory): void
    {
        $item = $inventory->item ?? Item::find($inventory->item_id);

        if (!$item) {
            throw ValidationException::withMessages(['item_id' => 'Master Item tidak ditemukan.']);
        }

        if (empty($inventory->location_id)) {
            throw ValidationException::withMessages(['location_id' => 'Lokasi wajib diisi.']);
        }

        if (empty($inventory->code)) {
            $prefix = $item->code;
            $dateCode = now()->format('ymd');

            do {
                $randomSuffix = strtoupper(Str::random(4));
                $generatedCode = "{$prefix}-{$dateCode}-{$randomSuffix}";
            } while (InventoryItem::where('code', $generatedCode)->exists());

            $inventory->code = $generatedCode;
        }

        if ($item->type === ItemType::Fixed || $item->type === ItemType::Installed) {
            $inventory->quantity = 1;
        }
    }

    /**
     * Handle the InventoryItem "saving" event.
     * Fokus: Validasi Data sebelum disimpan (Create & Update)
     */
    public function saving(InventoryItem $inventory): void
    {
        if (!$inventory->relationLoaded('item')) {
            $inventory->load('item');
        }

        $item = $inventory->item;

        if ($item->type === ItemType::Consumable) {
            // --- VALIDASI CONSUMABLE ---
            if ($inventory->quantity < 0) {
                throw ValidationException::withMessages(['quantity' => 'Stok tidak boleh negatif.']);
            }
        }
    }

    /**
     * Handle the InventoryItem "deleting" event.
     * Fokus: Proteksi Hapus Data
     */
    public function deleting(InventoryItem $inventory): void
    {
        if (!$inventory->isForceDeleting()) {
            $item = $inventory->item ?? Item::find($inventory->item_id);

            if ($item->type === ItemType::Consumable) {
                // --- PROTEKSI CONSUMABLE ---
                if ($inventory->quantity > 0) {
                    throw ValidationException::withMessages([
                        'quantity' => "Gagal Hapus: Masih ada sisa stok ({$inventory->quantity} unit) di lokasi ini. Nol-kan stok terlebih dahulu atau gunakan fitur pemakaian.",
                    ]);
                }
            } else {
                // --- PROTEKSI FIXED ---
                if ($inventory->status === InventoryStatus::Borrowed) {
                    throw ValidationException::withMessages([
                        'status' => 'Gagal Hapus: Aset ini sedang dipinjam. Harap kembalikan terlebih dahulu sebelum menghapus.',
                    ]);
                }
            }
        }
    }
}
