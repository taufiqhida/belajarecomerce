<?php

namespace Database\Seeders;

use App\Models\ShippingZone;
use Illuminate\Database\Seeder;

class ShippingZoneSeeder extends Seeder
{
    // [kode kabupaten/kota (wilayah.id), nama, ongkir flat awal]. Ongkir hanya perkiraan awal:
    // sesuaikan di admin (menu Zona Ongkir). Daerah yang tidak ada di sini: admin menentukan ongkir per pesanan.
    private const ZONES = [
        ['33.01', 'Kabupaten Cilacap', 15000],
        ['33.02', 'Kabupaten Banyumas', 40000],
        ['33.05', 'Kabupaten Kebumen', 45000],
        ['33.03', 'Kabupaten Purbalingga', 50000],
        ['33.04', 'Kabupaten Banjarnegara', 55000],
    ];

    public function run(): void
    {
        foreach (self::ZONES as $i => [$code, $name, $fee]) {
            // firstOrCreate: ongkir yang sudah diubah admin tidak ditimpa saat seeder dijalankan ulang
            ShippingZone::firstOrCreate(
                ['regency_code' => $code, 'district_code' => null],
                ['name' => $name, 'province_code' => substr($code, 0, 2), 'fee' => $fee, 'is_active' => true, 'sort_order' => $i + 1]
            );
        }
    }
}
