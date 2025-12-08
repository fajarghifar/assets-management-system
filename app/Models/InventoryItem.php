<?php

namespace App\Models;

use App\Enums\ItemType;
use App\Enums\InventoryStatus;
use Illuminate\Database\Eloquent\Model;
use App\Observers\InventoryItemObserver;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(InventoryItemObserver::class)]
class InventoryItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'item_id',
        'location_id',
        'serial_number',
        'status',
        'quantity',
        'min_quantity',
        'notes',
    ];

    protected $casts = [
        'status' => InventoryStatus::class,
        'quantity' => 'integer',
        'min_quantity' => 'integer'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // --- Helper Logic ---

    public function isFixed()
    {
        return $this->item->type === ItemType::Fixed;
    }

    public function isConsumable()
    {
        return $this->item->type === ItemType::Consumable;
    }
}
