<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Enums\ItemType;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. BARANG TETAP (FIXED) - Alat & Aset
        // ==========================================
        $fixedItems = [
            // --- Tang & Pemotong ---
            'TANGPT' => 'Tang Potong',
            'TANGLC' => 'Tang Lancip',
            'TANGBS' => 'Tang Biasa',
            'TPKCL' => 'Tang Potong Kecil',
            'TLKCL' => 'Tang Lancip Kecil',
            'TBKCL' => 'Tang Biasa Kecil',
            'CRIMP' => 'Tang Crimping',
            'GNTBS' => 'Gunting Besar',
            'GNTKC' => 'Gunting Kecil',
            'CUTTER' => 'Pisau Cutter', // "Kater" & "Pisau Cutter"
            'GERGAJ' => 'Gergaji Kecil',

            // --- Obeng & Toolkit ---
            'OBLPT' => 'Obeng Set Laptop',
            'OB115' => 'Obeng Set 115 in 1',
            'OBKNG' => 'Obeng Kuning',
            'OBSTD' => 'Obeng Standar', // "Obeng"
            'TOOLKT' => 'Toolkit Satu Set', // "TOOLKIT SATU SET"
            'TKPALU' => 'Toolkit Set Lengkap (Palu dll)', // "1 set toolkit obeng palu dll"

            // --- Alat Listrik & Panas ---
            'GLUEGN' => 'Alat Lem Tembak (Glue Gun)',
            'BLOWER' => 'Blower / Heat Gun',
            'SUNTIK' => 'Suntikan Besar (Refill)',

            // --- Tester & Ukur ---
            'LANTST' => 'LAN Tester',
            'MULTI' => 'Multi Meter Digital',
            'OPM' => 'Optical Power Meter (OPM)',
            'PSTEST' => 'Power Supply Tester', // "Tester power suplay"
            'WTRPAS' => 'Waterpass', // "Matherpas"

            // --- Elektronik & Aset Kantor ---
            'UPS' => 'UPS',
            'HT' => 'Handy Talky (HT)',
            'STB' => 'STB (Set Top Box)',
            'IPPHON' => 'Fanvil (IP Phone)',
            'POE' => 'POE Injector',
            'HDDEXT' => 'Harddisk External',
            'KEYMIN' => 'Keyboard Mini',
            'HEADLP' => 'Lampu Kepala',

            // --- Converter / Adapter ---
            'CNHDMI' => 'Converter HDMI to USB',
            'CNVGA' => 'Converter VGA to USB',
        ];

        // ==========================================
        // 2. BARANG HABIS PAKAI (CONSUMABLE)
        // ==========================================
        $consumableItems = [
            'CLKIT' => 'Cleaning Kit',
            'RJ45' => 'Konektor RJ45',
            'RJ11' => 'Konektor RJ11',
            'FEMALE' => 'Konektor Female (Fimel)',
            'JACKDC' => 'Jack DC Male',
            'CMOS' => 'Baterai CMOS',
            'PASTA' => 'Pasta Processor (Thermal Paste)',
            'RG4' => 'Kabel RG4',
        ];

        // ==========================================
        // 3. BARANG TERPASANG (INSTALLED) - Sparepart
        // ==========================================
        $installedItems = [
            'WD500' => 'Harddisk WD 500GB',
            'SGT500' => 'Harddisk Seagate 500GB',
            'WD320' => 'Harddisk WD 320GB',
            'SGT1TB' => 'Harddisk Seagate 1TB',
            'SGT250' => 'Harddisk Seagate 250GB', // "Seagate 250GB"
            'HDDLAP' => 'Harddisk Laptop',
            'MOBO' => 'Motherboard', // "madherboard"
        ];

        // --- EKSEKUSI DATA ---
        $this->seedItems($fixedItems, ItemType::Fixed);
        $this->seedItems($consumableItems, ItemType::Consumable);
        $this->seedItems($installedItems, ItemType::Installed);
    }

    /**
     * Memproses array [CODE => NAME] dengan updateOrCreate
     * agar aman dijalankan berulang kali.
     */
    private function seedItems(array $items, ItemType $type): void
    {
        foreach ($items as $code => $name) {
            // Safety: Kode uppercase, max 20 char sesuai migrasi terakhir
            // (Sebelumnya kita sepakat user input manual max 20 char di migration Item)
            $cleanCode = strtoupper(trim($code));

            Item::updateOrCreate(
                ['code' => $cleanCode], // Cek berdasarkan Kode
                [
                    'name' => $name,
                    'type' => $type,
                    'description' => "Initial Import Data ({$type->getLabel()})",
                ]
            );
        }

        $this->command->info("✅ Sukses memproses " . count($items) . " items untuk kategori: {$type->getLabel()}");
    }
}
