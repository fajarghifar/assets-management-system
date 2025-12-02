<?php

namespace App\Models;

use App\Enums\ItemType;
use App\Observers\ItemObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(ItemObserver::class)]
class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'type',
        'description'
    ];

    protected $casts = [
        'type' => ItemType::class,
    ];

    public function stocks()
    {
        return $this->hasMany(ItemStock::class);
    }

    public function fixedInstances()
    {
        return $this->hasMany(FixedItemInstance::class);
    }

    public function installedInstances()
    {
        return $this->hasMany(InstalledItemInstance::class);
    }
}
