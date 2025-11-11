<?php

namespace App\Observers;

use App\Models\Item;
use App\Services\ItemManagementService;

class ItemObserver
{
    public function saving(Item $item)
    {
        if ($item->exists && $item->isDirty('type')) {
            throw new \LogicException('Mengubah tipe barang tidak diizinkan setelah item dibuat.');
        }
    }

    public function restored(Item $item)
    {
        (new ItemManagementService())->restore($item);
    }

    public function forceDeleted(Item $item)
    {
        (new ItemManagementService())->forceDelete($item);
    }
}
