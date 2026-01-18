<?php

namespace App\DTOs;

use App\Enums\ProductType;

class ProductData
{
    public function __construct(
        public readonly string $name,
        public readonly string $code,
        public readonly string $description,
        public readonly ProductType $type,
        public readonly int $category_id,
        public readonly bool $can_be_loaned,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'type' => $this->type,
            'category_id' => $this->category_id,
            'can_be_loaned' => $this->can_be_loaned,
        ];
    }
}
