<?php

namespace App\Models;

use App\Models\Area;
use App\Observers\LocationObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(LocationObserver::class)]
class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'area_id',
        'description',
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function itemStocks(): HasMany
    {
        return $this->hasMany(ItemStock::class);
    }

    public function fixedItemInstances(): HasMany
    {
        return $this->hasMany(FixedItemInstance::class);
    }

    public function installedItemInstances(): HasMany
    {
        return $this->hasMany(InstalledItemInstance::class);
    }

    public function installedItemHistory(): HasMany
    {
        return $this->hasMany(InstalledItemLocationHistory::class);
    }
}
