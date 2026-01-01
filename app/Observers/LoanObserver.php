<?php

namespace App\Observers;

use App\Models\Loan;
use Illuminate\Support\Str;

class LoanObserver
{
    public function creating(Loan $loan): void
    {
        if (empty($loan->code)) {
            // Format: B[YY][MM][DD][XXX] -> e.g. B260101ABC
            $datePrefix = 'B' . date('ymd');

            do {
                $randomChars = strtoupper(Str::random(3));
                $code = $datePrefix . $randomChars;
            } while (Loan::where('code', $code)->exists());

            $loan->code = $code;
        }
    }
}
