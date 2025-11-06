<?php

namespace App\Observers;

use App\Models\ItemStock;
use App\Services\ItemStockService;

class ItemStockObserver
{
    public function saving(ItemStock $stock)
    {
        (new ItemStockService())->validate($stock);
    }

    public function restored(ItemStock $stock)
    {
        (new ItemStockService())->restore($stock);
    }

    public function forceDeleted(ItemStock $stock)
    {
        (new ItemStockService())->forceDelete($stock);
    }
}
