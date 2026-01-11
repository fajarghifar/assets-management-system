<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Komputer & Laptop',
            'Aksesoris Komputer',
            'Perangkat Jaringan',
            'Keamanan & CCTV',
            'Peralatan Kerja',
            'Suku Cadang',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => 'Kategori untuk ' . $name
                ]
            );
        }

        $this->command->info('Categories seeded successfully.');
    }
}
