<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Enums\LocationSite;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $targetSites = [
            LocationSite::JMP2,
            LocationSite::TGS,
            LocationSite::BT,
        ];

        $roomName = 'Ruang IT';
        $codeSuffix = 'RIT';

        foreach ($targetSites as $site) {
            $manualCode = "{$site->value}-{$codeSuffix}";

            Location::firstOrCreate(
                [
                    'site' => $site->value,
                    'name' => $roomName,
                ],
                [
                    'code' => $manualCode,
                    'description' => "Pusat server dan operasional IT Staff di area {$site->getLabel()}.",
                ]
            );

            $this->command->info("✅ Lokasi dibuat: {$roomName} ({$manualCode}) di {$site->value}");
        }
    }
}
