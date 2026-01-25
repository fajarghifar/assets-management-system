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
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $itemNameToCode = $this->getItemMappings();
        $locationAliasToSite = [
            'TGS' => LocationSite::TGS,
            'BT Store' => LocationSite::BT,
            'JMP' => LocationSite::JMP2,
        ];

        // Prefetch Dependencies
        $products = Product::whereIn('code', array_unique($itemNameToCode))
            ->where('type', ProductType::Consumable)
            ->get()
            ->keyBy('code');

        if ($products->isEmpty()) {
            $this->command->error('No Consumable products found. Run ProductSeeder first.');
            return;
        }

        $allLocations = Location::all();
        $locationLookup = $this->resolveLocations($allLocations, $locationAliasToSite);
        $rawData = $this->getRawData();

        $totalConsumables = 0;
        $skipped = 0;

        foreach ($rawData as $row) {
            $itemName = trim($row['item']);
            $productCode = $itemNameToCode[$itemName] ?? null;

            if (!$productCode || !$products->has($productCode)) {
                continue; // Skip unknown or non-consumable items
            }

            $location = $locationLookup[$row['location']] ?? null;
            if (!$location) {
                $this->command->warn("⚠️ Location skipped: {$row['location']}");
                continue;
            }

            $qty = (int) $row['qty'];
            if ($qty <= 0)
                continue;

            $product = $products->get($productCode);

            // Upsert / Increment Stock
            $stock = ConsumableStock::firstOrNew([
                'product_id' => $product->id,
                'location_id' => $location->id,
            ]);

            $stock->quantity = ($stock->quantity ?? 0) + $qty;
            $stock->min_quantity = $stock->min_quantity ?? 5; // Default threshold
            $stock->save();

            $totalConsumables += $qty;
        }

        $this->command->info("🎉 CONSUMABLE SEEDER FINISHED");
        $this->command->info("   - Total Qty Added : $totalConsumables");
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
            'CLEANING KIT' => 'CLKIT',
            'BATERAI CIMOS' => 'CMOS',
            'JACK DC FEMALE' => 'FEMALE',
            'JACK DC MALE' => 'JACKDC',
            'PASTA' => 'PASTA',
            'RG 4' => 'RG4',
            'KONEKTOR RJ45' => 'RJ45',
            'RJ11' => 'RJ11',
            // Note: Assets like KEYBOARD MINI, MOTHERBOARD, etc. are intentionally omitted
            // as they should be handled by AssetSeeder if managed as Assets.
        ];
    }

    private function getRawData(): array
    {
        return [
            ['item' => 'CLEANING KIT', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'KONEKTOR RJ45', 'location' => 'JMP', 'qty' => 163],
            ['item' => 'BATERAI CIMOS', 'location' => 'JMP', 'qty' => 8],
            ['item' => 'RJ11', 'location' => 'JMP', 'qty' => 61],
            ['item' => 'PASTA', 'location' => 'JMP', 'qty' => 5],
            ['item' => 'JACK DC FEMALE', 'location' => 'JMP', 'qty' => 23],
            ['item' => 'JACK DC MALE', 'location' => 'JMP', 'qty' => 27],
            ['item' => 'RG 4', 'location' => 'JMP', 'qty' => 1],
        ];
    }
}
