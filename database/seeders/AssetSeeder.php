<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Product;
use App\Models\Location;
use App\Enums\AssetStatus;
use App\Enums\ProductType;
use App\Enums\LocationSite;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data Mapping
        $itemNameToCode = $this->getItemMappings();
        $locationAliasToSite = [
            'TGS' => LocationSite::TGS,
            'BT Store' => LocationSite::BT,
            'JMP' => LocationSite::JMP2,
        ];

        // Fetch Dependencies
        $products = Product::whereIn('code', array_unique($itemNameToCode))
            ->where('type', ProductType::Asset)
            ->get()
            ->keyBy('code');

        if ($products->isEmpty()) {
            $this->command->error('No Asset products found. Run ProductSeeder first.');
            return;
        }

        $allLocations = Location::all();
        $locationLookup = $this->resolveLocations($allLocations, $locationAliasToSite);
        $rawData = $this->getRawData();

        $totalAssets = 0;
        $now = now();
        $dateCode = $now->format('ymd');

        $this->command->info("Starting Asset Seeding...");

        foreach ($rawData as $row) {
            $itemName = trim($row['item']);
            $productCode = $itemNameToCode[$itemName] ?? null;

            if (!$productCode || !$products->has($productCode)) {
                continue; // Skip invalid products
            }

            $location = $locationLookup[$row['location']] ?? null;
            if (!$location) {
                continue; // Skip invalid locations
            }

            $product = $products->get($productCode);
            $qty = (int) $row['qty'];

            for ($i = 0; $i < $qty; $i++) {
                // Generate Unique Asset Tag
                do {
                    $randomCode = strtoupper(\Illuminate\Support\Str::random(4));
                    $assetTag = "INV.{$dateCode}.{$randomCode}";
                } while (Asset::where('asset_tag', $assetTag)->exists());

                Asset::create([
                    'product_id' => $product->id,
                    'location_id' => $location->id,
                    'asset_tag' => $assetTag,
                    'status' => AssetStatus::InStock,
                    'purchase_date' => $now->copy()->subDays(rand(1, 730)),
                    'notes' => "Migrasi Asset (Asal: {$row['location']}, Item: $itemName)",
                ]);
            }

            $totalAssets += $qty;
        }

        $this->command->info("🎉 ASSET SEEDER: Total Assets Created: $totalAssets");
    }

    private function resolveLocations($allLocations, $aliases): array
    {
        $lookup = [];
        foreach ($aliases as $alias => $siteEnum) {
            $siteLocations = $allLocations->where('site', $siteEnum);

            // Prefer IT locations, fallback to any
            $preferred = $siteLocations->first(
                fn($loc) =>
                str_contains(strtolower($loc->name), 'it') ||
                str_contains(strtolower($loc->name), 'server')
            );

            $lookup[$alias] = $preferred ?? $siteLocations->first();
        }
        return $lookup;
    }

    private function getItemMappings(): array
    {
        return [
            'TANG POTONG' => 'TANGPT',
            'TANG LANCIP' => 'TANGLC',
            'TANG BIASA' => 'TANGBS',
            'TANG POTONG KECIL' => 'TPKCL',
            'TANG LANCIP KECIL' => 'TLKCL',
            'TANG BIASA KECIL' => 'TBKCL',
            'TANG CRIMPING' => 'CRIMP',
            'GUNTING BESAR' => 'GNTBS',
            'GUNTING' => 'GNTBS',
            'GUNTING KECIL' => 'GNTKC',
            'PISAU CUTTER' => 'CUTTER',
            'KATER' => 'CUTTER',
            'GERGAJI KECIL' => 'GERGAJ',
            'OBENG SET LAPTOP' => 'OBLPT',
            'OBENG SET 115' => 'OB115',
            'OBENG KUNING' => 'OBKNG',
            'OBENG' => 'OBSTD',
            'OBENG STANDAR' => 'OBSTD',
            'TOOLKIT SATU SET' => 'TOOLKT',
            '1 SET TOOLKIT OBENG PALU Dll' => 'TKPALU',
            'ALAT LEM TEMBAK' => 'GLUEGN',
            'BLOWER' => 'BLOWER',
            'SUNTIKAN BESAR' => 'SUNTIK',
            'LAN TESTER' => 'LANTST',
            'TESTER LAN' => 'LANTST',
            'MULTI METER DIGITAL' => 'MULTI',
            'OPTICAL POWER METER (OPM)' => 'OPM',
            'POWER SUPPLY TESTER' => 'PSTEST',
            'MATHERPAS' => 'WTRPAS',
            'UPS' => 'UPS',
            'HT' => 'HT',
            'STB' => 'STB',
            'FANVIL' => 'IPPHON',
            'POE' => 'POE',
            'HARDISK EXTERNAL' => 'HDDEXT',
            'HARDIKS WD 500GB' => 'WD500',
            'HARDIKS SEAGATE 500GB' => 'SGT500',
            'HARDIKS WD 320GB' => 'WD320',
            'HARDIKS SEAGATE 1 TB' => 'SGT1TB',
            'SEAGATE 250GB' => 'SGT250',
            'HARDIKS LAPTOP' => 'HDDLAP',
        ];
    }

    private function getRawData(): array
    {
        return [
            ['item' => 'TANG POTONG', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'TANG LANCIP', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'TANG BIASA', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'LAN TESTER', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'TOOLKIT SATU SET', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'OBENG SET LAPTOP', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'TANG CRIMPING', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'ALAT LEM TEMBAK', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'PISAU CUTTER', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'MULTI METER DIGITAL', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'TANG CRIMPING', 'location' => 'BT Store', 'qty' => 1],
            ['item' => 'LAN TESTER', 'location' => 'BT Store', 'qty' => 1],
            ['item' => 'GUNTING KECIL', 'location' => 'BT Store', 'qty' => 1],
            ['item' => 'GERGAJI KECIL', 'location' => 'BT Store', 'qty' => 2],
            ['item' => 'OPTICAL POWER METER (OPM)', 'location' => 'BT Store', 'qty' => 1],
            ['item' => 'HARDISK EXTERNAL', 'location' => 'BT Store', 'qty' => 2],
            ['item' => 'OBENG SET 115', 'location' => 'BT Store', 'qty' => 1],
            ['item' => 'TANG BIASA KECIL', 'location' => 'BT Store', 'qty' => 1],
            ['item' => 'TANG LANCIP KECIL', 'location' => 'BT Store', 'qty' => 1],
            ['item' => 'TANG POTONG KECIL', 'location' => 'BT Store', 'qty' => 1],
            ['item' => '1 SET TOOLKIT OBENG PALU Dll', 'location' => 'BT Store', 'qty' => 1],
            ['item' => 'OBENG KUNING', 'location' => 'BT Store', 'qty' => 1],
            ['item' => 'FANVIL', 'location' => 'BT Store', 'qty' => 1],
            ['item' => 'SUNTIKAN BESAR', 'location' => 'BT Store', 'qty' => 3],
            ['item' => 'TANG CRIMPING', 'location' => 'JMP', 'qty' => 2],
            ['item' => 'HARDIKS WD 500GB', 'location' => 'JMP', 'qty' => 1],
            ['item' => 'HARDIKS SEAGATE 500GB', 'location' => 'JMP', 'qty' => 1],
            ['item' => 'HARDIKS WD 320GB', 'location' => 'JMP', 'qty' => 1],
            ['item' => 'HARDIKS SEAGATE 1 TB', 'location' => 'JMP', 'qty' => 2],
            ['item' => 'SEAGATE 250GB', 'location' => 'JMP', 'qty' => 1],
            ['item' => 'POWER SUPPLY TESTER', 'location' => 'JMP', 'qty' => 2],
            ['item' => 'HARDIKS LAPTOP', 'location' => 'JMP', 'qty' => 1],
            ['item' => 'TESTER LAN', 'location' => 'JMP', 'qty' => 1],
            ['item' => 'POE', 'location' => 'JMP', 'qty' => 1],
            ['item' => 'UPS', 'location' => 'JMP', 'qty' => 2],
            ['item' => 'HT', 'location' => 'JMP', 'qty' => 3],
            ['item' => 'BLOWER', 'location' => 'JMP', 'qty' => 1],
            ['item' => 'GUNTING', 'location' => 'TGS', 'qty' => 2],
            ['item' => 'OBENG', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'KATER', 'location' => 'TGS', 'qty' => 4],
            ['item' => 'BLOWER', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'TANG POTONG', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'TANG LANCIP', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'TANG BIASA', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'STB', 'location' => 'JMP', 'qty' => 1],
        ];
    }
}
