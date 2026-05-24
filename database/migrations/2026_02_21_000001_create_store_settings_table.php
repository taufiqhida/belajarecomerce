<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('whatsapp_number')->default('');
            $table->text('message_template')->nullable();
            $table->enum('site_mode', ['live', 'maintenance', 'coming_soon'])->default('live');
            $table->string('store_name')->default('Cold Storage');
            $table->text('store_description')->nullable();
            $table->string('store_logo')->nullable();
            $table->string('store_address')->nullable();
            $table->string('store_email')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};

