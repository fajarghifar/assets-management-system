<?php

namespace App\DTOs;

class ConsumableStockData
{
    public function __construct(
        public readonly int $product_id,
        public readonly int $location_id,
        public readonly int $quantity,
        public readonly int $min_quantity,
    ) {}

    public function toArray(): array
    {
        return [
            'product_id' => $this->product_id,
            'location_id' => $this->location_id,
            'quantity' => $this->quantity,
            'min_quantity' => $this->min_quantity,
        ];
    }
}
