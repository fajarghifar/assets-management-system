<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\Area;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        // Daftar Kode Area yang ingin ditambahkan Ruang IT
        $targetAreaCodes = ['JMP2', 'TGS', 'BT'];

        foreach ($targetAreaCodes as $code) {
            $area = Area::where('code', $code)->first();

            if ($area) {
                Location::firstOrCreate(
                    [
                        'area_id' => $area->id,
                        'name' => 'Ruang IT',
                    ],
                    [
                        'description' => "Ruang Server dan operasional IT Staff di area {$area->name}.",
                    ]
                );

                $this->command->info("✅ Lokasi 'Ruang IT' berhasil dibuat untuk Area: {$code}");
            } else {
                $this->command->warn("⚠️ Area dengan kode '{$code}' tidak ditemukan. Lewati.");
            }
        }
    }
}
