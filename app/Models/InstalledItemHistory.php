<?php

namespace App\Models;

use App\Models\InstalledItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InstalledItemHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'installed_item_id',
        'location_id',
        'installed_at',
        'removed_at',
        'notes',
    ];

    protected $casts = [
        'installed_at' => 'date',
        'removed_at' => 'date',
    ];

    public function installedItem()
    {
        return $this->belongsTo(InstalledItem::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
