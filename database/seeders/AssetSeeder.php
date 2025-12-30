<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Product;
use App\Models\Location;
use App\Enums\AssetStatus;
use App\Enums\ProductType;
use App\Enums\LocationSite;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $itemNameToCode = [
            // Fixed Items
            'TANG POTONG' => 'TANGPT',
            'TANG LANCIP' => 'TANGLC',
            'TANG BIASA' => 'TANGBS',
            'TANG POTONG KECIL' => 'TPKCL',
            'TANG LANCIP KECIL' => 'TLKCL',
            'TANG BIASA KECIL' => 'TBKCL',
            'TANG CRIMPING' => 'CRIMP',
            'GUNTING BESAR' => 'GNTBS',
            'GUNTING KECIL' => 'GNTKC',
            'GUNTING' => 'GNTKC', // Map generic to specific
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
            'MULTI METER DIGITAL' => 'MULTI',
            'OPTICAL POWER METER (OPM)' => 'OPM',
            'POWER SUPPLY TESTER' => 'PSTEST',
            'MATHERPAS' => 'WTRPAS',
            'UPS' => 'UPS',
            'HT' => 'HT',
            'STB' => 'STB',
            'FANVIL' => 'IPPHON',
            'POE' => 'POE',

            // Installed Items
            'HARDISK EXTERNAL' => 'HDDEXT',
            'HARDIKS WD 500GB' => 'WD500',
            'HARDIKS SEAGATE 500GB' => 'SGT500',
            'HARDIKS WD 320GB' => 'WD320',
            'HARDIKS SEAGATE 1 TB' => 'SGT1TB',
            'SEAGATE 250GB' => 'SGT250',
            'HARDIKS LAPTOP' => 'HDDLAP',
        ];

        // Raw Data
        $rawData = [
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
            ['item' => 'POWER SUPPLY TESTER', 'location' => 'JMP', 'qty' => 2], // Merged from duplicate entry
            ['item' => 'HARDIKS LAPTOP', 'location' => 'JMP', 'qty' => 1],
            ['item' => 'TESTER LAN', 'location' => 'JMP', 'qty' => 1], // Mapped to LANTST? Wait, 'TESTER LAN' not in map. 'LAN TESTER' is. 'TESTER LAN' needs mapping or fix. Assuming typo for 'LAN TESTER'.
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

        // Location Mapping
        $locationAliasToSite = [
            'TGS' => LocationSite::TGS,
            'BT Store' => LocationSite::BT,
            'JMP' => LocationSite::JMP2,
        ];

        $totalAssets = 0;
        $globalCounter = 1;

        foreach ($rawData as $row) {
            $itemName = trim($row['item']);

            // Fix Typo
            if ($itemName === 'TESTER LAN') $itemName = 'LAN TESTER';

            $locationAlias = $row['location'];
            $qty = (int) $row['qty'];

            if ($qty <= 0) continue;

            $productCode = $itemNameToCode[$itemName] ?? null;
            if (!$productCode) continue;

            $product = Product::where('code', $productCode)->first();
            if (!$product) {
                $this->command->error("❌ ERROR: Produk '$productCode' ($itemName) tidak ditemukan.");
                continue;
            }

            // Skip Consumables
            if ($product->type === ProductType::Consumable) {
                continue;
            }

            $location = null;
            if ($locationAlias) {
                $siteEnum = $locationAliasToSite[$locationAlias] ?? null;
                if ($siteEnum) {
                    $location = Location::where('site', $siteEnum)
                        ->where('name', 'like', '%Ruang IT%')
                        ->first()
                        ?? Location::where('site', $siteEnum)->first();
                }
            }

            for ($i = 0; $i < $qty; $i++) {
                $assetTag = sprintf('AST-%s-%s-%04d', date('Y'), $product->code, $globalCounter++);

                Asset::create([
                    'product_id'     => $product->id,
                    'location_id'    => $location?->id,
                    'asset_tag'      => $assetTag,
                    'status'         => AssetStatus::InStock,
                    'purchase_date'  => now()->subMonths(rand(1, 24)),
                    'purchase_price' => 0,
                    'notes'          => "Migrasi dari data lama (Lokasi Awal: $locationAlias)",
                ]);
            }

            $totalAssets += $qty;
        }

        $this->command->info("🎉 ASSET SEEDER: Total Aset Fisik Dibuat: $totalAssets");
    }
}
