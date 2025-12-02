<?php

namespace App\Observers;

use App\Models\Item;
use App\Enums\ItemType;
use Illuminate\Support\Str;
use App\Enums\FixedItemStatus;
use App\Models\FixedItemInstance;
use Illuminate\Validation\ValidationException;

class FixedItemInstanceObserver
{
    public function creating(FixedItemInstance $instance): void
    {
        $item = $instance->item ?? Item::find($instance->item_id);

        if (!$item || $item->type !== ItemType::Fixed) {
            throw ValidationException::withMessages(['item_id' => 'Item tidak valid.']);
        }

        if (empty($instance->code)) {
            $prefix = $item->code;

            $dateCode = now()->format('ymd');
            $randomSuffix = strtoupper(Str::random(4));

            $instance->code = "{$prefix}-{$dateCode}-{$randomSuffix}";
        }
    }

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
