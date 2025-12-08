<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Item;
use App\Enums\ItemType;
use App\Models\Location;
use App\Models\ItemStock;
use App\Models\InstalledItem;
use App\Models\InventoryItem;
use Illuminate\Database\Seeder;
use App\Models\FixedItemInstance;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Mapping eksplisit: [nama di data inventaris mentah => kode item di ItemSeeder]
        $itemNameToCode = [
            // --- Fixed Items ---
            'TANG POTONG' => 'TANGPT',
            'TANG LANCIP' => 'TANGLC',
            'TANG BIASA' => 'TANGBS',
            'TANG POTONG KECIL' => 'TPKCL',
            'TANG LANCIP KECIL' => 'TLKCL',
            'TANG BIASA KECIL' => 'TBKCL',
            'TANG CRIMPING' => 'CRIMP',
            'GUNTING BESAR' => 'GNTBS',
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
            'MULTI METER DIGITAL' => 'MULTI',
            'OPTICAL POWER METER (OPM)' => 'OPM',
            'POWER SUPPLY TESTER' => 'PSTEST',
            'MATHERPAS' => 'WTRPAS',
            'UPS' => 'UPS',
            'HT' => 'HT',
            'STB' => 'STB',
            'FANVIL' => 'IPPHON',
            'POE' => 'POE',

            // --- Installed Items (Sparepart) ---
            'HARDISK EXTERNAL' => 'HDDEXT',
            'HARDIKS WD 500GB' => 'WD500',
            'HARDIKS SEAGATE 500GB' => 'SGT500',
            'HARDIKS WD 320GB' => 'WD320',
            'HARDIKS SEAGATE 1 TB' => 'SGT1TB',
            'SEAGATE 250GB' => 'SGT250',
            'HARDIKS LAPTOP' => 'HDDLAP',

            // --- Consumable Items ---
            'CLEANING KIT' => 'CLKIT',
            'TESTER LAN' => 'LANTST',
            'BATERAI CIMOS' => 'CMOS',
            'FIMEL' => 'FEMALE',
            'PASTA' => 'PASTA',
            'JACK DC MALE' => 'JACKDC',
            'KEYBOARD MINI' => 'KEYMIN',
            'MADHERBOARD' => 'MOBO',
            'HDMI TO USB' => 'CNHDMI',
            'VGA TO USB' => 'CNVGA',
            'LAMPU KEPALA' => 'HEADLP',
            'RG 4' => 'RG4',
            'KONEKTOR RJ45' => 'RJ45',
            'RJ11' => 'RJ11',
        ];

        // Data inventaris mentah (langsung dari tabel Anda)
        $rawData = [
            ['item' => 'TANG POTONG', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'TANG LANCIP', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'TANG BIASA', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'CLEANING KIT', 'location' => 'TGS', 'qty' => 1],
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
            ['item' => 'POWER SUPPLY TESTER', 'location' => 'JMP', 'qty' => 1],
            ['item' => 'HARDIKS LAPTOP', 'location' => 'JMP', 'qty' => 1],
            ['item' => 'TESTER LAN', 'location' => 'JMP', 'qty' => 1],
            ['item' => 'POE', 'location' => 'JMP', 'qty' => 1],
            ['item' => 'UPS', 'location' => 'JMP', 'qty' => 2],
            ['item' => 'HT', 'location' => 'JMP', 'qty' => 3],
            ['item' => 'KONEKTOR RJ45', 'location' => 'JMP', 'qty' => 163],
            ['item' => 'BATERAI CIMOS', 'location' => 'JMP', 'qty' => 8],
            ['item' => 'MATHERPAS', 'location' => 'JMP', 'qty' => 2],
            ['item' => 'RJ11', 'location' => 'JMP', 'qty' => 61],
            ['item' => 'FIMEL', 'location' => 'JMP', 'qty' => 23],
            ['item' => 'PASTA', 'location' => 'JMP', 'qty' => 5],
            ['item' => 'JACK DC MALE', 'location' => 'JMP', 'qty' => 27],
            ['item' => 'BLOWER', 'location' => 'JMP', 'qty' => 1],
            ['item' => 'KEYBOARD MINI', 'location' => 'JMP', 'qty' => 1],
            ['item' => 'MADHERBOARD', 'location' => 'JMP', 'qty' => 1],
            ['item' => 'POWER SUPPLY TESTER', 'location' => 'JMP', 'qty' => 1],
            ['item' => 'HDMI TO USB', 'location' => 'JMP', 'qty' => 1],
            ['item' => 'VGA TO USB', 'location' => 'JMP', 'qty' => 4],
            ['item' => 'GUNTING', 'location' => 'TGS', 'qty' => 2],
            ['item' => 'OBENG', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'TANG CRIMPING', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'KATER', 'location' => 'TGS', 'qty' => 4],
            ['item' => 'BLOWER', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'TANG POTONG', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'TANG LANCIP', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'TANG BIASA', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'LAN TESTER', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'MULTI METER DIGITAL', 'location' => 'TGS', 'qty' => 1],
            ['item' => 'STB', 'location' => 'JMP', 'qty' => 1],
            ['item' => 'LAMPU KEPALA', 'location' => 'JMP', 'qty' => 2],
            ['item' => 'RG 4', 'location' => 'JMP', 'qty' => 1],
        ];

        // Mapping lokasi → area code
        $locationAliasToAreaCode = [
            'TGS' => 'TGS',
            'BT Store' => 'BT',
            'JMP' => 'JMP2',
        ];

        foreach ($rawData as $row) {
            $itemName = trim($row['item']);
            $locationAlias = $row['location'];
            $qty = (int) $row['qty'];

            if ($qty <= 0)
                continue;

            $itemCode = $itemNameToCode[$itemName] ?? null;
            if (!$itemCode) {
                $this->command->warn("⚠️ Tidak ada mapping untuk item: \"$itemName\"");
                continue;
            }

            $item = Item::where('code', $itemCode)->first();
            if (!$item) {
                $this->command->error("❌ Item \"$itemCode\" tidak ditemukan.");
                continue;
            }

            // Tentukan lokasi (termasuk fallback ke JMP untuk null)
            if ($locationAlias === null) {
                $area = Area::where('code', 'JMP2')->first();
                if (!$area) {
                    $this->command->warn("⚠️ Area JMP tidak ditemukan");
                    continue;
                }
                $location = Location::where('area_id', $area->id)
                    ->where('name', 'Ruang IT')
                    ->first();
                if (!$location) {
                    $this->command->warn("⚠️ Lokasi Ruang IT di JMP tidak ditemukan");
                    continue;
                }
            } else {
                $areaCode = $locationAliasToAreaCode[$locationAlias] ?? null;
                if (!$areaCode) {
                    $this->command->warn("⚠️ Alias lokasi tidak dikenali: \"$locationAlias\"");
                    continue;
                }
                $area = Area::where('code', $areaCode)->first();
                if (!$area) {
                    $this->command->warn("⚠️ Area tidak ditemukan: $areaCode");
                    continue;
                }
                $location = Location::where('area_id', $area->id)
                    ->where('name', 'Ruang IT')
                    ->first();
                if (!$location) {
                    $this->command->warn("⚠️ Lokasi 'Ruang IT' tidak ditemukan di area: {$area->name}");
                    continue;
                }
            }

            // ✅ PROSES BERDASARKAN TIPE ITEM
            if ($item->type === ItemType::Installed) {
                // --- SIMPAN KE TABEL TERPISAH: installed_item_instances ---
                for ($i = 0; $i < $qty; $i++) {
                    InstalledItem::create([
                        'item_id' => $item->id,
                        'location_id' => $location->id,
                        'installed_at' => now()->subDays(rand(30, 365)),
                        'serial_number' => null,
                        'notes' => 'Auto-seeded from inventory',
                    ]);
                }
                $this->command->info("📦 [INSTALLED] {$item->name} (x{$qty}) → {$location->name}");

            } elseif ($item->type === ItemType::Consumable) {
                // --- SIMPAN KE inventory_items (stok) ---
                $inventoryItem = InventoryItem::firstOrNew([
                    'item_id' => $item->id,
                    'location_id' => $location->id,
                ]);

                if ($inventoryItem->exists) {
                    $inventoryItem->quantity += $qty;
                } else {
                    $inventoryItem->quantity = $qty;
                    $inventoryItem->min_quantity = max(1, intval($qty * 0.2));
                }
                $inventoryItem->save();
                $this->command->info("📦 [CONSUMABLE] {$item->name} (x{$qty}) → {$location->name}");

            } elseif ($item->type === ItemType::Fixed) {
                // --- SIMPAN KE inventory_items (per unit) ---
                for ($i = 0; $i < $qty; $i++) {
                    InventoryItem::create([
                        'item_id' => $item->id,
                        'location_id' => $location->id,
                        'status' => 'available',
                        'serial_number' => null,
                        'notes' => 'Auto-seeded from inventory',
                    ]);
                }
                $this->command->info("📦 [FIXED] {$item->name} (x{$qty}) → {$location->name}");
            }
        }

        $this->command->info("✅ SEEDER SELESAI!");
        $this->command->info("- Inventory Items: " . InventoryItem::count());
        $this->command->info("- Installed Items: " . InstalledItem::count());
    }
}
