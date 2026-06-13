<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = [
        'store_name', 'store_lat', 'store_lng',
        'base_fare', 'per_km_rate', 'max_radius_km', 'min_order_for_delivery',
        'font_family', 'hero_title', 'hero_subtitle', 'hero_image_path',
        'about_text', 'about_image_path',
        'is_open', 'busy_mode', 'promo_banner_text',
    ];

    protected $casts = [
        'store_lat'              => 'float',
        'store_lng'              => 'float',
        'base_fare'              => 'decimal:2',
        'per_km_rate'            => 'decimal:2',
        'max_radius_km'          => 'decimal:2',
        'min_order_for_delivery' => 'decimal:2',
        'is_open'                => 'boolean',
        'busy_mode'              => 'boolean',
    ];

    public static function current(): self
    {
        $setting = self::query()->firstOrCreate([], [
            'store_name'             => 'Es Teh Jumbo',
            'store_lat'              => -6.2434,
            'store_lng'              => 106.9871,
            'base_fare'              => 5000,
            'per_km_rate'            => 2000,
            'max_radius_km'          => 5,
            'min_order_for_delivery' => 15000,
            'font_family'            => 'Plus Jakarta Sans',
            'hero_title'             => 'Seger<br>Sampai<br><span class="text-amber-300">Puas.</span>',
            'hero_subtitle'          => 'Es teh jumbo racikan harian, manis pas dan dingin maksimal. Mulai Rp 5.000, diantar cepat ke seluruh Galaxy, Bekasi.',
            'hero_image_path'        => 'images/3d-home.png',
            'about_text'             => 'Es Teh Jumbo lahir dari satu booth kecil di Permata Galaxy. Resepnya sederhana — daun teh pilihan, gula yang pas, es yang banyak, dan harga yang nggak bikin mikir dua kali. Kini kami antar ke seluruh radius 5 km, dengan rasa yang tetap sama.',
            'about_image_path'       => 'images/3d-about.png',
            'promo_banner_text'      => 'HAUS MELANDA? SEGARKAN HARIMU DENGAN ES TEH JUMBO CITA RASA OTENTIK MULAI RP 5.000! YUK PESAN SEKARANG!',
        ]);

        $dirty = false;
        if (is_null($setting->font_family)) { $setting->font_family = 'Plus Jakarta Sans'; $dirty = true; }
        if (is_null($setting->hero_title)) { $setting->hero_title = 'Seger<br>Sampai<br><span class="text-amber-300">Puas.</span>'; $dirty = true; }
        if (is_null($setting->hero_subtitle)) { $setting->hero_subtitle = 'Es teh jumbo racikan harian, manis pas dan dingin maksimal. Mulai Rp 5.000, diantar cepat ke seluruh Galaxy, Bekasi.'; $dirty = true; }
        if (is_null($setting->hero_image_path)) { $setting->hero_image_path = 'images/3d-home.png'; $dirty = true; }
        if (is_null($setting->about_text)) { $setting->about_text = 'Es Teh Jumbo lahir dari satu booth kecil di Permata Galaxy. Resepnya sederhana — daun teh pilihan, gula yang pas, es yang banyak, dan harga yang nggak bikin mikir dua kali. Kini kami antar ke seluruh radius 5 km, dengan rasa yang tetap sama.'; $dirty = true; }
        if (is_null($setting->about_image_path)) { $setting->about_image_path = 'images/3d-about.png'; $dirty = true; }
        if (is_null($setting->promo_banner_text)) { $setting->promo_banner_text = 'HAUS MELANDA? SEGARKAN HARIMU DENGAN ES TEH JUMBO CITA RASA OTENTIK MULAI RP 5.000! YUK PESAN SEKARANG!'; $dirty = true; }

        if ($dirty) {
            $setting->save();
        }

        return $setting;
    }
}
