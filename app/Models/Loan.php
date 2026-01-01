<?php

namespace App\Models;

use App\Enums\LoanStatus;
use App\Observers\LoanObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[ObservedBy(LoanObserver::class)]
class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'borrower_name',
        'code',
        'proof_image',
        'purpose',
        'loan_date',
        'due_date',
        'returned_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'loan_date' => 'datetime',
        'due_date' => 'datetime',
        'returned_date' => 'datetime',
        'status' => LoanStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loanItems(): HasMany
    {
        return $this->hasMany(LoanItem::class);
    }

    /**
     * Scope for loans that are still in progress (Approved or Overdue).
     */
    public function scopeOngoing($query)
    {
        return $query->whereIn('status', [LoanStatus::Approved, LoanStatus::Overdue]);
    }

    /**
     * Scope for overdue loans.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', LoanStatus::Overdue)
            ->orWhere(fn($q) => $q->where('status', LoanStatus::Approved)->where('due_date', '<', now()));
    }
}
