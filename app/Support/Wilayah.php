<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Daftar wilayah Indonesia dari wilayah.id (data Kemendagri), di-cache 30 hari.
 * Level: provinces, regencies (induk: provinsi), districts (induk: kabupaten), villages (induk: kecamatan).
 */
class Wilayah
{
    /** Panjang kode induk: provinsi "33", kabupaten "33.01", kecamatan "33.01.01" */
    public const PARENT_LENGTH = ['provinces' => null, 'regencies' => 2, 'districts' => 5, 'villages' => 8];

    /** @return array<int, array{id: string, name: string}>|null  null = sumber tidak tersedia */
    public static function list(string $level, ?string $parentId = null): ?array
    {
        $path = $level === 'provinces' ? 'provinces.json' : "{$level}/{$parentId}.json";
        $key = "wilayah2:$path";

        $data = Cache::get($key);
        if ($data !== null) {
            return $data;
        }

        try {
            $res = Http::timeout(8)->get("https://wilayah.id/api/{$path}");
        } catch (\Throwable $e) {
            return null;
        }
        $rows = $res->successful() ? $res->json('data') : null;
        if (!is_array($rows)) {
            return null;
        }

        // hanya simpan id + nama; kegagalan tidak di-cache
        $data = array_map(fn($r) => ['id' => (string) $r['code'], 'name' => (string) $r['name']], $rows);
        Cache::put($key, $data, now()->addDays(30));

        return $data;
    }

    /** [id => nama] untuk dropdown admin; kosong kalau sumber tidak tersedia */
    public static function options(string $level, ?string $parentId = null): array
    {
        if ($level !== 'provinces' && !$parentId) {
            return [];
        }
        return collect(self::list($level, $parentId) ?? [])->pluck('name', 'id')->all();
    }

    /** Nama tampilan zona: "Kabupaten Cilacap" atau "Kec. Kesugihan, Kabupaten Cilacap" */
    public static function zoneLabel(string $regencyCode, ?string $districtCode = null): string
    {
        $regency = self::options('regencies', substr($regencyCode, 0, 2))[$regencyCode] ?? $regencyCode;
        if (!$districtCode) {
            return $regency;
        }
        $district = self::options('districts', $regencyCode)[$districtCode] ?? $districtCode;
        return "Kec. {$district}, {$regency}";
    }
}
