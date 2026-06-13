<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name')->default('Es Teh Jumbo');
            $table->decimal('store_lat', 10, 7);
            $table->decimal('store_lng', 10, 7);
            $table->decimal('base_fare', 10, 2)->default(5000);
            $table->decimal('per_km_rate', 10, 2)->default(2000);
            $table->decimal('max_radius_km', 6, 2)->default(5);
            $table->decimal('min_order_for_delivery', 10, 2)->default(15000);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
