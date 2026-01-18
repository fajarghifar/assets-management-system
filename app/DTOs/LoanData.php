<?php

namespace App\DTOs;

use Illuminate\Http\UploadedFile;

class LoanData
{
    /**
     * @param LoanItemData[] $items
     */
    public function __construct(
        public readonly string $loan_date,
        public readonly string $due_date,
        public readonly string $purpose,
        public readonly ?int $user_id = null,
        public readonly ?string $borrower_name = null,
        public readonly ?string $code = null,
        public readonly ?string $notes = null,
        public readonly string|UploadedFile|null $proof_image = null,
        public readonly array $items = [],
    ) {}

    public static function fromArray(array $data): self
    {
        $items = [];
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                $items[] = LoanItemData::fromArray($item);
            }
        }

        return new self(
            loan_date: $data['loan_date'],
            due_date: $data['due_date'],
            purpose: $data['purpose'],
            user_id: isset($data['user_id']) ? (int) $data['user_id'] : null,
            borrower_name: $data['borrower_name'] ?? null,
            code: $data['code'] ?? null,
            notes: $data['notes'] ?? null,
            proof_image: $data['proof_image'] ?? null,
            items: $items,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'user_id' => $this->user_id,
            'borrower_name' => $this->borrower_name,
            'code' => $this->code,
            'purpose' => $this->purpose,
            'loan_date' => $this->loan_date,
            'due_date' => $this->due_date,
            'notes' => $this->notes,
            'proof_image' => $this->proof_image,
            'items' => array_map(fn(LoanItemData $item) => $item->toArray(), $this->items),
        ], fn($value) => !is_null($value));
    }
}
