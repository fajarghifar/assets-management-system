<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'status' => 'string',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class)->withTrashed();
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
