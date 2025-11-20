<?php

namespace App\Observers;

use App\Models\Area;
use Illuminate\Validation\ValidationException;

class AreaObserver
{
    public function deleting(Area $area): void
    {
        if ($area->locations()->exists()) {
            throw ValidationException::withMessages([
                'area' => "Gagal: Area '{$area->name}' tidak bisa dihapus karena masih memiliki Lokasi terdaftar.",
            ]);
        }
    }
}
