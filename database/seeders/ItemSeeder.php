<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // === BARANG HABIS PAKAI (consumable) ===
        Item::create([
            'code' => 'RJ45-8P8C',
            'name' => 'Konektor RJ45',
            'type' => 'consumable',
            'description' => 'Konektor jaringan 8P8C untuk crimping',
        ]);

        Item::create([
            'code' => 'CABLE-LAN-CAT6',
            'name' => 'Kabel LAN Cat6 2m',
            'type' => 'consumable',
            'description' => 'Kabel jaringan UTP Cat6, panjang 2 meter',
        ]);

        Item::create([
            'code' => 'SSD-256GB',
            'name' => 'SSD 256GB SATA',
            'type' => 'consumable',
            'description' => 'Solid State Drive untuk upgrade laptop',
        ]);

        Item::create([
            'code' => 'LABEL-TUBE',
            'name' => 'Label Kabel Heat Shrink',
            'type' => 'consumable',
            'description' => 'Label identifikasi kabel tahan panas',
        ]);

        // === BARANG TETAP (fixed) ===
        Item::create([
            'code' => 'TANG-CRIMP',
            'name' => 'Tang Crimping Belden',
            'type' => 'fixed',
            'description' => 'Alat untuk crimping konektor RJ45',
        ]);

        Item::create([
            'code' => 'MULTIMETER-DT',
            'name' => 'Multimeter Digital DT9205A',
            'type' => 'fixed',
            'description' => 'Alat ukur tegangan, arus, resistansi',
        ]);

        // === BARANG TERPASANG (installed) ===
        Item::create([
            'code' => 'LAPTOP-DELL-3400',
            'name' => 'Laptop Dell Latitude 3400',
            'type' => 'installed',
            'description' => 'Laptop untuk staff operasional dan desain',
        ]);
        Item::create([
            'code' => 'PROJ-EPSON-EBU',
            'name' => 'Proyektor Epson EBU',
            'type' => 'installed',
            'description' => 'Proyektor untuk presentasi di ruang meeting',
        ]);
        Item::create([
            'code' => 'PRINTER-HP-MFP',
            'name' => 'Printer HP LaserJet MFP',
            'type' => 'installed',
            'description' => 'Printer multifungsi untuk dokumen',
        ]);
        Item::create([
            'code' => 'WIFI-AP-RUIJIE',
            'name' => 'Wi-Fi AP Ruijie RG-AP860',
            'type' => 'installed',
            'description' => 'Access point nirkabel untuk jaringan kantor & toko',
        ]);

        Item::create([
            'code' => 'SWITCH-TP-LINK',
            'name' => 'Switch TP-Link 8-Port',
            'type' => 'installed',
            'description' => 'Switch jaringan untuk distribusi LAN',
        ]);

        Item::create([
            'code' => 'CCTV-HIKVISION',
            'name' => 'Kamera CCTV Hikvision',
            'type' => 'installed',
            'description' => 'Kamera pengawas untuk area toko dan gudang',
        ]);
    }
}
