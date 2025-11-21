<?php

namespace App\Models;

use App\Models\Item;
use App\Enums\FixedItemStatus;
use Illuminate\Database\Eloquent\Model;
use App\Observers\FixedItemInstanceObserver;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(FixedItemInstanceObserver::class)]
class FixedItemInstance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'item_id',
        'serial_number',
        'status',
        'location_id',
        'notes'
    ];

    protected $casts = [
        'status' => FixedItemStatus::class,
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
