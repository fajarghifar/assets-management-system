<?php

namespace App\DTOs;

use App\Enums\AssetStatus;
use Illuminate\Http\UploadedFile;

class AssetData
{
    public function __construct(
        public readonly int $product_id,
        public readonly int $location_id,
        public readonly ?string $asset_tag = null,
        public readonly ?string $serial_number = null,
        public readonly ?AssetStatus $status = null,
        public readonly ?string $purchase_date = null, // Y-m-d format
        public readonly string|UploadedFile|null $image_path = null,
        public readonly ?string $notes = null,

        // History specific fields
        public readonly ?string $recipient_name = null,
        public readonly ?string $history_notes = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            product_id: (int) $data['product_id'],
            location_id: (int) $data['location_id'],
            asset_tag: $data['asset_tag'] ?? null,
            serial_number: $data['serial_number'] ?? null,
            status: isset($data['status'])
                ? (is_string($data['status']) ? AssetStatus::tryFrom($data['status']) : $data['status'])
                : null,
            purchase_date: $data['purchase_date'] ?? null,
            image_path: $data['image_path'] ?? null,
            notes: $data['notes'] ?? null,
            recipient_name: $data['recipient_name'] ?? null,
            history_notes: $data['history_notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'product_id' => $this->product_id,
            'location_id' => $this->location_id,
            'asset_tag' => $this->asset_tag,
            'serial_number' => $this->serial_number,
            'status' => $this->status,
            'purchase_date' => $this->purchase_date,
            'image_path' => $this->image_path,
            'notes' => $this->notes,
        ], fn($value) => !is_null($value));
    }
}
