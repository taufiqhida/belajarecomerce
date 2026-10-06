<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Stok flash sale dihitung dalam kg
        Schema::table('flash_sales', function (Blueprint $table) {
            $table->decimal('flash_stock', 10, 2)->default(0)->change();
        });

        // Catat kg yang diambil dari kuota flash sale, agar bisa dikembalikan saat pesanan dibatalkan
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('flash_sale_id')->nullable()->after('weight_kg');
            $table->decimal('flash_kg', 10, 2)->default(0)->after('flash_sale_id');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', fn (Blueprint $t) => $t->dropColumn(['flash_sale_id', 'flash_kg']));
        Schema::table('flash_sales', fn (Blueprint $t) => $t->integer('flash_stock')->default(0)->change());
    }
};
