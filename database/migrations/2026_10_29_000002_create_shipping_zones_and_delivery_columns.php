<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Zona ongkir: nama daerah + tarif, diatur admin
        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('fee', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_method', 20)->default('delivery')->after('customer_address'); // delivery | pickup
            $table->unsignedBigInteger('shipping_zone_id')->nullable()->after('delivery_method');
            $table->string('shipping_zone_name')->nullable()->after('shipping_zone_id'); // salinan nama, agar tetap benar jika zona diubah/dihapus
            $table->decimal('shipping_fee', 12, 2)->default(0)->after('shipping_zone_name');
        });
    }

    public function down(): void
    {
        Schema::table('orders', fn (Blueprint $t) => $t->dropColumn(['delivery_method', 'shipping_zone_id', 'shipping_zone_name', 'shipping_fee']));
        Schema::dropIfExists('shipping_zones');
    }
};
