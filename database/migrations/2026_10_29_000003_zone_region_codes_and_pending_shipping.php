<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Zona = kabupaten/kota dari API wilayah (tarif flat), opsional dipersempit ke satu kecamatan
        Schema::table('shipping_zones', function (Blueprint $table) {
            $table->string('province_code', 10)->nullable()->after('name');
            $table->string('regency_code', 10)->nullable()->after('province_code')->index();
            $table->string('district_code', 12)->nullable()->after('regency_code');
        });

        Schema::table('orders', function (Blueprint $table) {
            // true = daerah belum punya tarif; admin menentukan ongkir lalu mengirim invoice
            $table->boolean('shipping_pending')->default(false)->after('shipping_fee');
            // batas bayar; pesanan pending dibatalkan otomatis setelah lewat batas ini
            $table->timestamp('payment_due_at')->nullable()->after('shipping_pending');
        });
    }

    public function down(): void
    {
        Schema::table('orders', fn (Blueprint $t) => $t->dropColumn(['shipping_pending', 'payment_due_at']));
        Schema::table('shipping_zones', fn (Blueprint $t) => $t->dropColumn(['province_code', 'regency_code', 'district_code']));
    }
};
