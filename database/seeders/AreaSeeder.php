<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        // === KANTOR (office) ===
        Area::create([
            'code' => 'OFF-JMP1',
            'name' => 'JMP 1',
            'category' => 'office',
            'address' => 'Jl. H. Abas No.48, Trusmi Kulon, Kec. Weru, Kabupaten Cirebon, Jawa Barat 45154',
        ]);

        Area::create([
            'code' => 'OFF-JMP2',
            'name' => 'JMP 2',
            'category' => 'office',
            'address' => 'Jl. H. Abas No.48, Trusmi Kulon, Kec. Weru, Kabupaten Cirebon, Jawa Barat 45154',
        ]);

        // === TOKO (store) ===
        Area::create([
            'code' => 'STORE-BT',
            'name' => 'BT Batik Trusmi',
            'category' => 'store',
            'address' => 'Jl. Trusmi No.148, Weru Lor, Kec. Plered, Kabupaten Cirebon, Jawa Barat 45154',
        ]);
    }
}
