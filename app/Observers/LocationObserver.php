<?php

namespace App\Observers;

use App\Models\Location;
use Illuminate\Validation\ValidationException;

class LocationObserver
{
    public function deleting(Location $location): void
    {
        if ($location->itemStocks()->where('quantity', '>', 0)->exists()) {
            throw ValidationException::withMessages([
                'location' => "Gagal: Masih ada stok barang (Consumable) yang tersimpan di lokasi '{$location->name}'.",
            ]);
        }

        if ($location->fixedItemInstances()->exists()) {
            throw ValidationException::withMessages([
                'location' => "Gagal: Lokasi '{$location->name}' masih digunakan oleh Aset Tetap (Fixed Items).",
            ]);
        }

        if ($location->installedItemInstances()->exists()) {
            throw ValidationException::withMessages([
                'location' => "Gagal: Lokasi '{$location->name}' masih digunakan oleh Aset Terpasang (Installed Items).",
            ]);
        }
    }
}
