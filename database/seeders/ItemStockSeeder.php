<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Location;
use App\Models\ItemStock;
use Illuminate\Database\Seeder;

class ItemStockSeeder extends Seeder
{
    public function run(): void
    {
        $jmp1Wh = Location::where('code', 'JMP1-WH1')->first(); // Gudang JMP 1
        $btWh = Location::where('code', 'BT-WH')->first();      // Gudang BT

        if (!$jmp1Wh || !$btWh) {
            $this->command->error('Lokasi gudang tidak ditemukan!');
            return;
        }

        // RJ45
        ItemStock::create([
            'item_id' => Item::where('code', 'RJ45-8P8C')->value('id'),
            'location_id' => $jmp1Wh->id,
            'quantity' => 500,
            'min_quantity' => 50,
        ]);

        ItemStock::create([
            'item_id' => Item::where('code', 'RJ45-8P8C')->value('id'),
            'location_id' => $btWh->id,
            'quantity' => 300,
            'min_quantity' => 30,
        ]);

        // Kabel LAN
        ItemStock::create([
            'item_id' => Item::where('code', 'CABLE-LAN-CAT6')->value('id'),
            'location_id' => $jmp1Wh->id,
            'quantity' => 100,
            'min_quantity' => 10,
        ]);

        ItemStock::create([
            'item_id' => Item::where('code', 'CABLE-LAN-CAT6')->value('id'),
            'location_id' => $btWh->id,
            'quantity' => 80,
            'min_quantity' => 8,
        ]);

        // SSD
        ItemStock::create([
            'item_id' => Item::where('code', 'SSD-256GB')->value('id'),
            'location_id' => $jmp1Wh->id,
            'quantity' => 15,
            'min_quantity' => 3,
        ]);

        // Label Kabel
        ItemStock::create([
            'item_id' => Item::where('code', 'LABEL-TUBE')->value('id'),
            'location_id' => $jmp1Wh->id,
            'quantity' => 200,
            'min_quantity' => 20,
        ]);

        $this->command->info('✅ ItemStockSeeder: stok barang habis pakai berhasil di-seed.');
    }
}
