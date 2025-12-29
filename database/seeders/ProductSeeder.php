<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Enums\ProductType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil ID Kategori Baru [slug => id]
        $categories = Category::pluck('id', 'slug');

        if ($categories->isEmpty()) {
            throw new \Exception("❌ ERROR: Kategori kosong. Jalankan CategorySeeder dulu!");
        }

        $getCatId = fn($slug) => $categories[$slug]
            ?? throw new \Exception("❌ ERROR: Slug '$slug' tidak ditemukan.");

        // Mapping ID Kategori Baru
        $catIds = [
            'TOOLS' => $getCatId('perkakas-peralatan'),
            'TEST' => $getCatId('alat-ukur-pengujian'),
            'NET' => $getCatId('infrastruktur-jaringan'),
            'COMP' => $getCatId('komponen-komputer'),
            'PERI' => $getCatId('periferal-aksesoris'),
            'POWR' => $getCatId('sistem-daya-listrik'),
            'MAINT' => $getCatId('perlengkapan-perawatan'),
        ];

        DB::transaction(function () use ($catIds) {
            // ==========================================
            // A. SEED ASSETS (Barang Tetap/Unit)
            // ==========================================

            // 1. Kategori: TOOLS & EQUIPMENT
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

            // 2. Kategori: TESTING INSTRUMENTS
            $this->seedBatch([
                'LANTST' => 'LAN Tester',
                'MULTI'  => 'Multi Meter Digital',
                'OPM'    => 'Optical Power Meter (OPM)',
                'PSTEST' => 'Power Supply Tester',
                'WTRPAS' => 'Waterpass',
            ], ProductType::Asset, $catIds['TEST'], true);

            // 3. Kategori: NETWORK INFRASTRUCTURE (Hardware)
            $this->seedBatch([
                'HT'     => 'Handy Talky (HT)',
                'IPPHON' => 'Fanvil (IP Phone)',
                'POE'    => 'POE Injector',
            ], ProductType::Asset, $catIds['NET'], true);

            // 4. Kategori: POWER SYSTEMS
            $this->seedBatch([
                'UPS' => 'UPS 600VA',
            ], ProductType::Asset, $catIds['POWR'], false);

            // 5. Kategori: COMPUTER COMPONENTS (Storage/Part Bernilai)
            $this->seedBatch([
                'WD500'  => 'Harddisk WD 500GB',
                'SGT500' => 'Harddisk Seagate 500GB',
                'WD320'  => 'Harddisk WD 320GB',
                'SGT1TB' => 'Harddisk Seagate 1TB',
                'SGT250' => 'Harddisk Seagate 250GB',
                'HDDLAP' => 'Harddisk Laptop (General)',
                'MOBO'   => 'Motherboard PC',
            ], ProductType::Asset, $catIds['COMP'], false);

            // 6. Kategori: PERIPHERALS & ACCESSORIES
            $this->seedBatch([
                'STB'    => 'STB (Set Top Box)',
                'HDDEXT' => 'Harddisk External',
                'KEYMIN' => 'Keyboard Mini',
                'HEADLP' => 'Lampu Kepala',
                'CNHDMI' => 'Converter HDMI to USB',
                'CNVGA'  => 'Converter VGA to USB',
            ], ProductType::Asset, $catIds['PERI'], true);


            // ==========================================
            // B. SEED CONSUMABLES (Barang Habis Pakai)
            // ==========================================

            // 1. Masuk ke NETWORK (Konektor & Kabel)
            $this->seedBatch([
                'RJ45'   => 'Konektor RJ45',
                'RJ11'   => 'Konektor RJ11',
                'FEMALE' => 'Konektor Female',
                'RG4'    => 'Kabel RG4 / Coaxial',
            ], ProductType::Consumable, $catIds['NET'], true);

            // 2. Masuk ke MAINTENANCE SUPPLIES (Bahan & Sparepart Kecil)
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
        $count = 0;
        foreach ($items as $code => $name) {
            Product::updateOrCreate(
                ['code' => strtoupper(trim($code))],
                [
                    'name' => $name,
                    'type' => $type,
                    'category_id' => $categoryId,
                    'can_be_loaned' => $isLoanable,
                    'description' => "Initial Import ({$type->getLabel()})",
                ]
            );
            $count++;
        }
        $this->command->info("✅ Seeded {$count} items | Loanable: " . ($isLoanable ? 'YES' : 'NO'));
    }
}
