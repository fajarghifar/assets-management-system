<?php

namespace App\Models;

use App\Models\Item;
use App\Models\Location;
use App\Models\InstalledItemHistory;
use Illuminate\Database\Eloquent\Model;
use App\Observers\InstalledItemObserver;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(InstalledItemObserver::class)]
class InstalledItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'item_id',
        'serial_number',
        'location_id',
        'installed_at',
        'notes'
    ];

    protected $casts = [
        'installed_at' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function histories()
    {
        return $this->hasMany(InstalledItemHistory::class, 'installed_item_id');
    }
}
