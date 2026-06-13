<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->string('font_family')->default('Plus Jakarta Sans');
            $table->text('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->string('hero_image_path')->default('images/3d-home.png');
            $table->text('about_text')->nullable();
            $table->string('about_image_path')->default('images/3d-about.png');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'font_family',
                'hero_title',
                'hero_subtitle',
                'hero_image_path',
                'about_text',
                'about_image_path',
            ]);
        });
    }
};
