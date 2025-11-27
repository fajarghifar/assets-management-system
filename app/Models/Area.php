<?php

namespace App\Models;

use App\Models\Location;
use App\Enums\AreaCategory;
use App\Observers\AreaObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(AreaObserver::class)]
class Area extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category',
        'address',
    ];

    protected $casts = [
        'category' => AreaCategory::class,
    ];

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }
}
