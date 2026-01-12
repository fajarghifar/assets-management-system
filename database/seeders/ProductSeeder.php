<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Enums\ProductType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Category IDs
        // Mapping from 'Actual Slug in DB' to 'Code in Seeder Logic'
        // Categories in DB: komputer-laptop, aksesoris-komputer, perangkat-jaringan, keamanan-cctv, peralatan-kerja, suku-cadang

        $categories = Category::all()->pluck('id', 'slug');

        if ($categories->isEmpty()) {
            throw new \Exception("❌ ERROR: Kategori kosong. Jalankan CategorySeeder dulu!");
        }

        $getCatId = fn($slug) => $categories[$slug]
            ?? throw new \Exception("❌ ERROR: Slug '$slug' tidak ditemukan di database. Pastikan CategorySeeder sudah dijalankan.");

        // Map Category IDs based on existing CategorySeeder data
        $catIds = [
            'TOOLS' => $getCatId('peralatan-kerja'),      // Tang, Obeng -> Peralatan Kerja
            'TEST'  => $getCatId('peralatan-kerja'),      // Multimeter -> Peralatan Kerja
            'NET'   => $getCatId('perangkat-jaringan'),   // HT, IP Phone, RJ45 -> Perangkat Jaringan
            'COMP'  => $getCatId('komputer-laptop'),      // HDD, Mobo -> Komputer & Laptop
            'PERI'  => $getCatId('aksesoris-komputer'),   // Keyboard, Mouse -> Aksesoris Komputer
            'POWR'  => $getCatId('aksesoris-komputer'),   // UPS -> Aksesoris Komputer
            'MAINT' => $getCatId('suku-cadang'),          // Pasta, CMOS -> Suku Cadang (Spare Parts)
        ];

        DB::transaction(function () use ($catIds) {
            // Seed Assets

            // Tools (Peralatan Kerja)
            $this->seedBatch([
                'TANGPT' => 'Tang Potong',
                'TANGLC' => 'Tang Lancip',
                'TANGBS' => 'Tang Biasa',
                'TPKCL'  => 'Tang Potong Kecil',
                'TLKCL'  => 'Tang Lancip Kecil',
                'TBKCL'  => 'Tang Biasa Kecil',
                'CRIMP'  => 'Tang Crimping',
                'GNTBS'  => 'Gunting Besar',
                'GNTKC'  => 'Gunting Kecil',
                'CUTTER' => 'Pisau Cutter',
                'GERGAJ' => 'Gergaji Kecil',
                'OBLPT'  => 'Obeng Set Laptop',
                'OB115'  => 'Obeng Set 115 in 1',
                'OBKNG'  => 'Obeng Kuning',
                'OBSTD'  => 'Obeng Standar',
                'TOOLKT' => 'Toolkit Satu Set',
                'TKPALU' => 'Toolkit Set Lengkap',
                'GLUEGN' => 'Alat Lem Tembak (Glue Gun)',
                'BLOWER' => 'Blower / Heat Gun',
                'SUNTIK' => 'Suntikan Besar (Refill)',
            ], ProductType::Asset, $catIds['TOOLS'], true);

            // Testing (Peralatan Kerja)
            $this->seedBatch([
                'LANTST' => 'LAN Tester',
                'MULTI'  => 'Multi Meter Digital',
                'OPM'    => 'Optical Power Meter (OPM)',
                'PSTEST' => 'Power Supply Tester',
                'WTRPAS' => 'Waterpass',
            ], ProductType::Asset, $catIds['TEST'], true);

            // Network Hardware (Perangkat Jaringan)
            $this->seedBatch([
                'HT'     => 'Handy Talky (HT)',
                'IPPHON' => 'Fanvil (IP Phone)',
                'POE'    => 'POE Injector',
            ], ProductType::Asset, $catIds['NET'], true);

            // Power Systems (Aksesoris Komputer)
            $this->seedBatch([
                'UPS' => 'UPS 600VA',
            ], ProductType::Asset, $catIds['POWR'], false);

            // Components (Komputer & Laptop)
            $this->seedBatch([
                'WD500'  => 'Harddisk WD 500GB',
                'SGT500' => 'Harddisk Seagate 500GB',
                'WD320'  => 'Harddisk WD 320GB',
                'SGT1TB' => 'Harddisk Seagate 1TB',
                'SGT250' => 'Harddisk Seagate 250GB',
                'HDDLAP' => 'Harddisk Laptop (General)',
                'MOBO'   => 'Motherboard PC',
            ], ProductType::Asset, $catIds['COMP'], false);

            // Peripherals (Aksesoris Komputer)
            $this->seedBatch([
                'STB'    => 'STB (Set Top Box)',
                'HDDEXT' => 'Harddisk External',
                'KEYMIN' => 'Keyboard Mini',
                'HEADLP' => 'Lampu Kepala',
                'CNHDMI' => 'Converter HDMI to USB',
                'CNVGA'  => 'Converter VGA to USB',
            ], ProductType::Asset, $catIds['PERI'], true);


            // Seed Consumables

            // Network Consumables (Perangkat Jaringan)
            $this->seedBatch([
                'RJ45'   => 'Konektor RJ45',
                'RJ11'   => 'Konektor RJ11',
                'FEMALE' => 'Konektor Female',
                'RG4'    => 'Kabel RG4 / Coaxial',
            ], ProductType::Consumable, $catIds['NET'], true);

            // Maintenance (Suku Cadang)
            $this->seedBatch([
                'CLKIT'  => 'Cleaning Kit',
                'PASTA'  => 'Pasta Processor (Thermal Paste)',
                'CMOS'   => 'Baterai CMOS 2032',
                'JACKDC' => 'Jack DC Male',
            ], ProductType::Consumable, $catIds['MAINT'], true);

        });
    }

    private function seedBatch(array $items, ProductType $type, int $categoryId, bool $isLoanable = true): void
    {
        $now = now();
        $data = [];

        foreach ($items as $code => $name) {
            $data[] = [
                'code' => strtoupper(trim($code)),
                'name' => $name,
                'type' => $type->value,
                'category_id' => $categoryId,
                'can_be_loaned' => $isLoanable,
                'description' => "Initial Import ({$type->getLabel()})",
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Upsert based on 'code' unique column
        Product::upsert(
            $data,
            ['code'], // Unique constraint
            ['name', 'type', 'category_id', 'can_be_loaned', 'description', 'updated_at'] // Update these if exists
        );

        $this->command->info("✅ Seeded " . count($data) . " items | Type: {$type->name}");
    }
}
