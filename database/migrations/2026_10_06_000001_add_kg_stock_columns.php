<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Stok produk dihitung dalam kg (boleh desimal)
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('stock', 10, 2)->default(0)->change();
        });

        // Berat (kg) tiap varian / paket
        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('weight_kg', 8, 2)->default(1)->after('type');
        });

        // Total kg per baris pesanan, untuk mengembalikan stok saat dibatalkan
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('weight_kg', 10, 2)->default(0)->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', fn (Blueprint $t) => $t->dropColumn('weight_kg'));
        Schema::table('product_variants', fn (Blueprint $t) => $t->dropColumn('weight_kg'));
        Schema::table('products', fn (Blueprint $t) => $t->integer('stock')->default(0)->change());
    }
};
