<?php

namespace App\Observers;

use App\Models\Item;
use App\Enums\ItemType;
use Illuminate\Validation\ValidationException;

class ItemObserver
{
    public function updating(Item $item): void
    {
        if ($item->isDirty('type')) {
            $originalType = $item->getOriginal('type');

            if ($originalType instanceof ItemType) {
                $relation = $originalType->getRelationName();
                if ($item->$relation()->exists()) {
                    throw ValidationException::withMessages([
                        'type' => 'Tipe barang tidak dapat diubah karena sudah memiliki data transaksi/stok terkait.',
                    ]);
                }
            }
        }
    }

    public function deleting(Item $item): void
    {
        if (!$item->isForceDeleting()) {
            $item->ensureCanBeDeleted();

            $relation = $item->type->getRelationName();
            $item->$relation()->delete();
        }
    }

    public function restoring(Item $item): void
    {
        $relation = $item->type->getRelationName();
        $item->$relation()->withTrashed()->restore();
    }

    public function forceDeleting(Item $item): void
    {
        $relation = $item->type->getRelationName();
        $item->$relation()->withTrashed()->forceDelete();
    }
}
