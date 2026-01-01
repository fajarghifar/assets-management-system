<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\LoanItem;
use App\Enums\LoanStatus;
use App\Enums\ProductType;
use App\Enums\AssetStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanApprovalService
{
    /**
     * Approve a loan and update inventory/asset status.
     */
    public function approve(Loan $loan): void
    {
        // Only Pending loans can be approved
        if ($loan->status !== LoanStatus::Pending) {
            return;
        }

        DB::transaction(function () use ($loan) {
            // Refresh to ensure we have the latest data & lock for safety
            $loan->refresh();

            foreach ($loan->loanItems as $loanItem) {
                // Lock the related asset or consumable stock row to prevent race conditions
                if ($loanItem->type === ProductType::Consumable) {
                    $loanItem->consumableStock()->lockForUpdate()->first();
                } else {
                    $loanItem->asset()->lockForUpdate()->first();
                }

                $this->processItemApproval($loanItem);
            }

            // Update Loan status
            $loan->update([
                'status' => LoanStatus::Approved,
                'notes' => trim($loan->notes . "\n[System] Approved at " . now()->format('d M Y H:i')),
            ]);
        });
    }

    /**
     * Process individual loan items (decrease stock or set asset status).
     */
    private function processItemApproval(LoanItem $loanItem): void
    {
        if ($loanItem->type === ProductType::Consumable) {
            $stock = $loanItem->consumableStock;

            // Check stock availability
            if ($stock->quantity < $loanItem->quantity_borrowed) {
                throw ValidationException::withMessages([
                    'error' => "Stok '{$stock->product->name}' tidak mencukupi."
                ]);
            }

            $stock->decrement('quantity', $loanItem->quantity_borrowed);

        } else {
            $asset = $loanItem->asset;

            // Check asset availability
            if ($asset->status !== AssetStatus::InStock) {
                throw ValidationException::withMessages([
                    'error' => "Aset '{$asset->product->name}' ({$asset->code}) sedang tidak tersedia ({$asset->status->getLabel()})."
                ]);
            }

            $asset->update(['status' => AssetStatus::Loaned]);
        }
    }

    /**
     * Reject a loan request.
     */
    public function reject(Loan $loan, string $reason): void
    {
        if ($loan->status !== LoanStatus::Pending) {
            return;
        }

        $loan->update([
            'status' => LoanStatus::Rejected,
            'notes' => trim($loan->notes . "\n[System] Rejected: $reason"),
        ]);
    }
}
