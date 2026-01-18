<?php

namespace App\DTOs;

use App\Enums\LoanItemType;

class LoanItemData
{
    public function __construct(
        public readonly LoanItemType $type,
        public readonly int $quantity_borrowed,
        public readonly ?int $asset_id = null,
        public readonly ?int $consumable_stock_id = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'] instanceof LoanItemType ? $data['type'] : LoanItemType::from($data['type']),
            quantity_borrowed: (int) $data['quantity_borrowed'],
            asset_id: isset($data['asset_id']) ? (int) $data['asset_id'] : null,
            consumable_stock_id: isset($data['consumable_stock_id']) ? (int) $data['consumable_stock_id'] : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'type' => $this->type->value,
            'quantity_borrowed' => $this->quantity_borrowed,
            'asset_id' => $this->asset_id,
            'consumable_stock_id' => $this->consumable_stock_id,
        ], fn($value) => !is_null($value));
    }
}
