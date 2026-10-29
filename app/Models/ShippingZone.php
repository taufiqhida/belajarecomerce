<?php

namespace App\Models;

use App\Support\Wilayah;
use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    protected $fillable = ['name', 'province_code', 'regency_code', 'district_code', 'fee', 'is_active', 'sort_order'];

    protected $casts = [
        'fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        // Nama zona otomatis dari wilayah yang dipilih admin (kecuali nama diisi manual)
        static::saving(function (ShippingZone $zone) {
            if ($zone->regency_code && (blank($zone->name) || (!$zone->isDirty('name') && $zone->isDirty(['regency_code', 'district_code'])))) {
                $zone->name = Wilayah::zoneLabel($zone->regency_code, $zone->district_code);
            }
            if ($zone->regency_code && blank($zone->province_code)) {
                $zone->province_code = substr($zone->regency_code, 0, 2);
            }
        });
    }

    public static function activeList()
    {
        return static::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
    }

    /** Zona untuk satu alamat: tarif kecamatan lebih diutamakan, lalu tarif kabupaten/kota */
    public static function forRegion(?string $regencyCode, ?string $districtCode): ?self
    {
        if (!$regencyCode) {
            return null;
        }
        $zones = static::where('is_active', true)->where('regency_code', $regencyCode)->get();

        return ($districtCode ? $zones->firstWhere('district_code', $districtCode) : null)
            ?? $zones->first(fn($z) => $z->district_code === null);
    }
}
