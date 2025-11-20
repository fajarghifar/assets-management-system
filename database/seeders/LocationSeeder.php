<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\Area;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil area
        $jmp1 = Area::where('code', 'OFF-JMP1')->first();
        $jmp2 = Area::where('code', 'OFF-JMP2')->first();
        $bt = Area::where('code', 'STORE-BT')->first();

        if (!$jmp1 || !$jmp2 || !$bt) {
            $this->command->error('Area tidak ditemukan! Pastikan AreaSeeder sudah dijalankan.');
            return;
        }

        // === LOKASI DI JMP 1 (KANTOR) ===
        Location::create([
            'code' => 'JMP1-RM1',
            'name' => 'Ruang Meeting 1',
            'area_id' => $jmp1->id,
            'description' => 'Ruang meeting kecil di lantai 1 untuk presentasi',
        ]);

        Location::create([
            'code' => 'JMP1-RM2',
            'name' => 'Ruang Meeting Besar',
            'area_id' => $jmp1->id,
            'description' => 'Ruang meeting utama dengan proyektor dan kapasitas 20 orang',
        ]);

        Location::create([
            'code' => 'JMP1-SRV',
            'name' => 'Ruang Server',
            'area_id' => $jmp1->id,
            'description' => 'Akses terbatas, hanya untuk tim IT',
        ]);

        Location::create([
            'code' => 'JMP1-WH1',
            'name' => 'Gudang Barang Jadi 1',
            'area_id' => $jmp1->id,
            'description' => 'Penyimpanan produk jadi dan peralatan IT',
        ]);

        // === LOKASI DI JMP 2 (KANTOR) ===
        Location::create([
            'code' => 'JMP2-RM1',
            'name' => 'Ruang Rapat Produksi',
            'area_id' => $jmp2->id,
            'description' => 'Untuk koordinasi tim produksi batik',
        ]);

        Location::create([
            'code' => 'JMP2-LAB',
            'name' => 'Laboratorium Desain',
            'area_id' => $jmp2->id,
            'description' => 'Area eksklusif desainer batik',
        ]);

        // === LOKASI DI BT BATIK TRUSMI (TOKO) ===
        Location::create([
            'code' => 'BT-SHOP1',
            'name' => 'Area Display Utama',
            'area_id' => $bt->id,
            'description' => 'Area pameran dan penjualan batik',
        ]);

        Location::create([
            'code' => 'BT-EVT',
            'name' => 'Ruang Event & Workshop',
            'area_id' => $bt->id,
            'description' => 'Untuk workshop batik atau acara khusus pelanggan',
        ]);

        Location::create([
            'code' => 'BT-WH',
            'name' => 'Gudang Retail',
            'area_id' => $bt->id,
            'description' => 'Stok barang retail dan perlengkapan toko',
        ]);

        $this->command->info('✅ LocationSeeder: 9 lokasi berhasil di-seed.');
    }
}
