<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Location;
use App\Enums\ProductType;
use App\Enums\LocationSite;
use App\Models\ConsumableStock;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ConsumableStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mapping: Raw Item Name -> Product Code in DB (from ProductSeeder)
        $itemNameToCode = [
            // Consumable Items
            'CLEANING KIT'   => 'CLKIT',
            'BATERAI CIMOS'  => 'CMOS',
            'JACK DC FEMALE' => 'FEMALE', // Mapped to 'FEMALE' (Konektor Female)
            'JACK DC MALE'   => 'JACKDC', // Mapped to 'JACKDC'
            'PASTA'          => 'PASTA',
            'KEYBOARD MINI'  => 'KEYMIN', // Asset, will be skipped
            'MADHERBOARD'    => 'MOBO',   // Asset, will be skipped
            'HDMI TO USB'    => 'CNHDMI', // Asset, will be skipped
            'VGA TO USB'     => 'CNVGA',  // Asset, will be skipped
            'LAMPU KEPALA'   => 'HEADLP', // Asset, will be skipped
            'RG 4'           => 'RG4',
            'KONEKTOR RJ45'  => 'RJ45',
            'RJ11'           => 'RJ11',
        ];

        // Raw Data from legacy/request
        $rawData = [
            ['item' => 'CLEANING KIT', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'KONEKTOR RJ45', 'location' => 'JMP', 'qty' => 163],
            ['item' => 'BATERAI CIMOS', 'location' => 'JMP', 'qty' => 8],
            // ['item' => 'MATHERPAS', 'location' => 'JMP', 'qty' => 2], // Unknown Item (Likely Waterpass - Asset)
            ['item' => 'RJ11', 'location' => 'JMP', 'qty' => 61],
            ['item' => 'PASTA', 'location' => 'JMP', 'qty' => 5],
            ['item' => 'JACK DC FEMALE', 'location' => 'JMP', 'qty' => 23],
            ['item' => 'JACK DC MALE', 'location' => 'JMP', 'qty' => 27],
            // ['item' => 'KEYBOARD MINI', 'location' => 'JMP', 'qty' => 1], // Asset
            // ['item' => 'MADHERBOARD', 'location' => 'JMP', 'qty' => 1], // Asset (Motherboard)
            // ['item' => 'HDMI TO USB', 'location' => 'JMP', 'qty' => 1], // Asset
            // ['item' => 'VGA TO USB', 'location' => 'JMP', 'qty' => 4], // Asset
            // ['item' => 'LAMPU KEPALA', 'location' => 'JMP', 'qty' => 2], // Asset
            ['item' => 'RG 4', 'location' => 'JMP', 'qty' => 1],
        ];

        // Location Mapping
        $locationAliasToSite = [
            'TGS' => LocationSite::TGS,
            'BT Store' => LocationSite::BT,
            'JMP' => LocationSite::JMP2,
        ];

        $totalConsumables = 0;
        $skipped = 0;

        foreach ($rawData as $row) {
            $itemName = trim($row['item']);
            $locationAlias = $row['location'];
            $qty = (int) $row['qty'];

            if ($qty <= 0) continue;

            $productCode = $itemNameToCode[$itemName] ?? null;
            if (!$productCode) {
                // $this->command->warn("⚠️ Skipped Unknown Item: $itemName");
                continue;
            }

            $product = Product::where('code', $productCode)->first();
            if (!$product) {
                continue;
            }

            if ($product->type !== ProductType::Consumable) {
                $skipped++;
                // $this->command->info("ℹ️ Skipped Asset: {$product->name} ($productCode)");
                continue;
            }

            $location = null;
            if ($locationAlias) {
                $siteEnum = $locationAliasToSite[$locationAlias] ?? null;
                if ($siteEnum) {
                    // Try to find specific room first, else general site location
                    $location = Location::where('site', $siteEnum)
                        ->where('name', 'like', '%Ruang IT%')
                        ->first()
                        ?? Location::where('site', $siteEnum)->first();
                }
            }

            if (!$location) {
                $this->command->error("❌ Location not found for alias: $locationAlias");
                continue;
            }

            // Upsert Logic manually to handle quantity addition
            $stock = ConsumableStock::where('product_id', $product->id)
                ->where('location_id', $location->id)
                ->first();

            if ($stock) {
                $stock->quantity += $qty;
                $stock->save();
            } else {
                ConsumableStock::create([
                    'product_id' => $product->id,
                    'location_id' => $location->id,
                    'quantity' => $qty,
                    'min_quantity' => 5, // Default threshold
                ]);
            }

            $totalConsumables += $qty;
        }

        $this->command->info("🎉 CONSUMABLE SEEDER FINISHED");
        $this->command->info("   - Total Qty Added : $totalConsumables");
        $this->command->info("   - Items Skipped   : $skipped (Assets/Unknown)");
    }
}
