<?php

namespace App\Observers;

use App\Models\Item;
use Illuminate\Validation\ValidationException;

class ItemObserver
{
    public function creating(Item $item): void
    {
        if (!empty($item->code)) {
            $item->code = strtoupper(trim($item->code));
        }
    }

    public function updating(Item $item): void
    {
        if ($item->isDirty('code')) {
            $relation = $item->type->getRelationName();
            if ($item->$relation()->exists()) {
                throw ValidationException::withMessages([
                    'code' => 'Kode barang tidak boleh diubah karena sudah memiliki stok/aset turunan.',
                ]);
            }
            $item->code = strtoupper(trim($item->code));
        }

        if ($item->isDirty('type')) {
            $relation = $item->type->getRelationName();
            if ($item->$relation()->exists()) {
                throw ValidationException::withMessages(['type' => 'Tipe barang tidak dapat diubah karena sudah ada data transaksi.']);
            }
        }
    }

    public function deleting(Item $item): void
    {
        if (!$item->isForceDeleting()) {
            $item->type->validateDeletion($item);

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
