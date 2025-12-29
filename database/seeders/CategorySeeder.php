<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // 1. TOOLS -> Perkakas & Peralatan
            [
                'name' => 'Perkakas & Peralatan',
                'slug' => 'perkakas-peralatan',
                'description' => 'Perkakas kerja teknisi, alat mekanik, dan peralatan listrik (Tang, Obeng, Blower, Solder).',
            ],

            // 2. TESTING -> Alat Ukur & Pengujian
            [
                'name' => 'Alat Ukur & Pengujian',
                'slug' => 'alat-ukur-pengujian',
                'description' => 'Alat pengujian dan pengukuran parameter jaringan/listrik (LAN Tester, Multimeter, OPM).',
            ],

            // 3. NETWORK -> Infrastruktur Jaringan
            [
                'name' => 'Infrastruktur Jaringan',
                'slug' => 'infrastruktur-jaringan',
                'description' => 'Perangkat keras jaringan, komunikasi, pengkabelan, dan konektor (Switch, HT, RJ45, Kabel LAN).',
            ],

            // 4. COMPONENTS -> Komponen Komputer
            [
                'name' => 'Komponen Komputer',
                'slug' => 'komponen-komputer',
                'description' => 'Komponen internal PC/Laptop dan media penyimpanan (Motherboard, HDD, SSD, RAM).',
            ],

            // 5. PERIPHERALS -> Periferal & Aksesoris
            [
                'name' => 'Periferal & Aksesoris',
                'slug' => 'periferal-aksesoris',
                'description' => 'Perangkat tambahan komputer dan adapter (Keyboard, Mouse, Converter HDMI/VGA).',
            ],

            // 6. POWER -> Sistem Daya & Listrik
            [
                'name' => 'Sistem Daya & Listrik',
                'slug' => 'sistem-daya-listrik',
                'description' => 'Perangkat catu daya dan backup listrik (UPS, Stabilizer, Power Supply).',
            ],

            // 7. MAINTENANCE -> Perlengkapan Perawatan
            [
                'name' => 'Perlengkapan Perawatan',
                'slug' => 'perlengkapan-perawatan',
                'description' => 'Bahan pendukung perawatan dan perbaikan (Thermal Paste, Cleaning Kit, Timah Solder, Isolasi).',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                ]
            );
        }
    }
}
