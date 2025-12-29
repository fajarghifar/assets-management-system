<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    public function saving(Product $product): void
    {
        if ($product->isDirty('code') && !empty($product->code)) {
            $product->code = strtoupper(trim($product->code));
        }
    }
}
