<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Enums\AreaCategory;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            [
                'code' => 'JMP1',
                'name' => 'JMP 1',
                'category' => AreaCategory::Office,
                'address' => 'Jl. H. Abas No.48, Trusmi Kulon, Kec. Weru, Kabupaten Cirebon, Jawa Barat 45154',
            ],
            [
                'code' => 'JMP2',
                'name' => 'JMP 2',
                'category' => AreaCategory::Office,
                'address' => 'Jl. H. Abas No.48, Trusmi Kulon, Kec. Weru, Kabupaten Cirebon, Jawa Barat 45154',
            ],
            [
                'code' => 'BT',
                'name' => 'BT Batik Trusmi',
                'category' => AreaCategory::Store,
                'address' => 'Jl. Trusmi No.148, Weru Lor, Kec. Plered, Kabupaten Cirebon, Jawa Barat 45154',
            ],
            [
                'code' => 'TGS',
                'name' => 'Tegalsari',
                'category' => AreaCategory::Office,
                'address' => 'Tegalsari, Kec. Plered, Kabupaten Cirebon, Jawa Barat 45154',
            ],
        ];

        foreach ($areas as $data) {
            Area::firstOrCreate(
                ['code' => $data['code']],
                $data
            );
        }
    }
}
