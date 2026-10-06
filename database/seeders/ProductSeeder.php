<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /** Kategori = kelompok jenis ikan. [slug => [nama, urutan]] */
    private const CATEGORIES = [
        'tuna' => ['Tuna', 1],
        'cakalang' => ['Cakalang', 2],
        'layang' => ['Layang', 3],
        'lemadang' => ['Lemadang', 4],
    ];

    /** [nama, harga per kg, badge, kategori]. Stok produk dihitung dalam kg. */
    private const PRODUCTS = [
        ['meka 25 up', 65000, 'best_seller', 'tuna'],
        ['albacore', 44000, 'best_seller', 'tuna'],
        ['Tuna 20 up YF', 54000, 'best_seller', 'tuna'],
        ['Tuna kecil 10up', 25000, 'best_seller', 'tuna'],
        ['meka 10 up', 55000, 'limited', 'tuna'],
        ['cakalang', 26000, 'best_seller', 'cakalang'],
        ['layang', 20000, 'best_seller', 'layang'],
        ['lemadang 1-2kg (2 down)', 20000, 'limited', 'lemadang'],
        ['lemadang 2-4kg (2up)', 40000, 'best_seller', 'lemadang'],
        ['lemadang 4up', 55000, 'best_seller', 'lemadang'],
    ];

    /** Pilihan pembelian (kg) per varian. */
    private const PACKS = [1, 5, 10];

    public function run(): void
    {
        $categories = [];
        foreach (self::CATEGORIES as $slug => [$label, $order]) {
            $categories[$slug] = Category::updateOrCreate(
                ['slug' => $slug],
                ['name' => $label, 'icon' => 'heroicon-o-fish', 'sort_order' => $order, 'is_active' => true]
            );
        }

        foreach (self::PRODUCTS as $i => [$name, $perKg, $badge, $cat]) {
            $existing = Product::where('slug', Str::slug($name))->exists();
            $product = Product::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'category_id' => $categories[$cat]->id,
                    'name' => $name,
                    'description' => "Ikan {$name}, dijual per kg. Harga Rp " . number_format($perKg, 0, ',', '.') . "/kg.",
                    'base_price' => $perKg,
                    'badge' => $badge,
                    'is_active' => true,
                    'sort_order' => $i,
                ] + ($existing ? [] : ['stock' => 0, 'modal_price' => 0]) // isi stok & modal asli di admin; seeder tidak menimpa data yang sudah ada
            );

            foreach (self::PACKS as $j => $kg) {
                $product->variants()->updateOrCreate(
                    ['name' => "{$kg} kg"],
                    [
                        'type' => 'weight',
                        'weight_kg' => $kg,
                        'price' => $perKg * $kg,
                        'stock' => 0,
                        'is_active' => true,
                        'sort_order' => $j,
                    ]
                );
            }
        }

        // Kategori contoh lama yang tidak dipakai produk apa pun dibersihkan
        Category::whereIn('slug', ['ikan', 'ikan-nila', 'ikan-lele', 'ikan-kakap', 'ikan-patin', 'ikan-gurame'])
            ->whereDoesntHave('products')
            ->delete();
    }
}
