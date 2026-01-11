<?php

namespace App\Models;

use App\Enums\LocationSite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'site',
        'name',
        'description',
    ];

    protected $casts = [
        'site' => LocationSite::class,
    ];
}
