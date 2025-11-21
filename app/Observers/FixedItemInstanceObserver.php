<?php

namespace App\Observers;

use App\Enums\ItemType;
use App\Enums\FixedItemStatus;
use App\Models\FixedItemInstance;
use Illuminate\Validation\ValidationException;

class FixedItemInstanceObserver
{
    public function saving(FixedItemInstance $instance): void
    {
        if (!$instance->relationLoaded('item')) {
            $instance->load('item');
        }

        if ($instance->item && $instance->item->type !== ItemType::Fixed) {
            throw ValidationException::withMessages([
                'item_id' => 'Instance hanya bisa dibuat untuk barang bertipe Tetap (Fixed).',
            ]);
        }

        if ($instance->status === FixedItemStatus::Available && empty($instance->location_id)) {
            throw ValidationException::withMessages([
                'location_id' => 'Lokasi wajib diisi jika status aset Tersedia.',
            ]);
        }
    }

    public function deleting(FixedItemInstance $instance): void
    {
        if (!$instance->isForceDeleting()) {
            if ($instance->status === FixedItemStatus::Borrowed) {
                throw ValidationException::withMessages([
                    'status' => 'Gagal Hapus: Aset ini sedang dipinjam. Harap kembalikan terlebih dahulu.',
                ]);
            }
        }
    }
}
