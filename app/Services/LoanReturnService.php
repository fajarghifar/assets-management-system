<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\LoanItem;
use App\Enums\LoanStatus;
use App\Enums\ProductType;
use App\Enums\AssetStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanReturnService
{
    /**
     * Process logic for returning items (partial or full).
     */
    public function processReturn(Loan $loan, LoanItem $loanItem, int $returnQty, ?string $conditionNotes = null): void
    {
        DB::transaction(function () use ($loan, $loanItem, $returnQty, $conditionNotes) {

            // Handle Inventory Updates
            if ($loanItem->type === ProductType::Consumable) {
                // For consumables, ensure we don't return more than borrowed? Validation should be in UI.
                if ($returnQty > 0) {
                    $loanItem->consumableStock->increment('quantity', $returnQty);
                }
            } else {
                // For assets, set status back to InStock
                if ($returnQty > 0 && $loanItem->asset_id) {
                    $loanItem->asset->update([
                        'status' => AssetStatus::InStock,
                        // Logic for location assignment can be refined here if needed
                    ]);
                }
            }

            // Update Loan Item Data
            if ($returnQty > 0) {
                $loanItem->increment('quantity_returned', $returnQty);

                // If fully returned, mark completion timestamp
                if ($loanItem->quantity_returned >= $loanItem->quantity_borrowed) {
                    $loanItem->update(['returned_at' => now()]);
                }
            }

            // Check if the entire Loan is completed
            $this->checkLoanCompletion($loan);
        });
    }

    /**
     * Check if all items in a loan have been returned, and close the loan if so.
     */
    private function checkLoanCompletion(Loan $loan): void
    {
        $loan->refresh(); // Load fresh relation data

        $allReturned = $loan->loanItems->every(
            fn($item) => $item->quantity_returned >= $item->quantity_borrowed
        );

        if ($allReturned) {
            $loan->update([
                'status' => LoanStatus::Closed,
                'returned_date' => now(),
            ]);
        }
    }
}
