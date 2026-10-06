<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /** Harga per kg. Stok produk dihitung dalam kg. */
    private const PRODUCTS = [
        ['meka 25 up', 65000, 'best_seller'],
        ['albacore', 44000, 'best_seller'],
        ['Tuna 20 up YF', 54000, 'best_seller'],
        ['cakalang', 26000, 'best_seller'],
        ['layang', 20000, 'best_seller'],
        ['meka 10 up', 55000, 'limited'],
        ['lemadang 1-2kg (2 down)', 20000, 'limited'],
        ['lemadang 2-4kg (2up)', 40000, 'best_seller'],
        ['lemadang 4up', 55000, 'best_seller'],
        ['Tuna kecil 10up', 25000, 'best_seller'],
    ];

    /** Pilihan pembelian (kg) per varian. */
    private const PACKS = [1, 5, 10];

    public function run(): void
    {
        $category = Category::firstOrCreate(
            ['slug' => 'ikan'],
            ['name' => 'Ikan', 'icon' => 'heroicon-o-fish', 'sort_order' => 0]
        );

        foreach (self::PRODUCTS as $i => [$name, $perKg, $badge]) {
            $existing = Product::where('slug', Str::slug($name))->exists();
            $product = Product::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'category_id' => $category->id,
                    'name' => $name,
                    'description' => "Ikan {$name}, dijual per kg. Harga Rp " . number_format($perKg, 0, ',', '.') . "/kg.",
                    'base_price' => $perKg,
                    'modal_price' => round($perKg * 0.85),
                    'badge' => $badge,
                    'is_active' => true,
                    'sort_order' => $i,
                ] + ($existing ? [] : ['stock' => 100]) // stok awal (kg), tidak menimpa stok yang sudah berjalan
            );

            foreach (self::PACKS as $j => $kg) {
                $product->variants()->updateOrCreate(
                    ['name' => "{$kg} kg"],
                    [
                        'type' => 'weight',
                        'weight_kg' => $kg,
                        'price' => $perKg * $kg,
                        'modal_price' => round($perKg * 0.85) * $kg,
                        'stock' => 0,
                        'is_active' => true,
                        'sort_order' => $j,
                    ]
                );
            }
        }
    }
}
