<?php

namespace App\Services;

use App\Models\StoreSetting;

/**
 * Hitung ongkir berdasarkan jarak (Haversine) dan tarif berjenjang.
 *
 * Properti yang dijaga:
 *  - Hasil ongkir monotonic non-decreasing terhadap jarak.
 *  - Lokasi di luar max_radius_km akan dilempar exception saat calculateOngkir().
 */
class ShippingCalculator
{
    private const EARTH_RADIUS_KM = 6371.0;

    public function __construct(private ?StoreSetting $settings = null)
    {
        $this->settings = $settings ?? StoreSetting::current();
    }

    /**
     * Jarak Haversine antara dua koordinat dalam km.
     */
    public function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_KM * $c;
    }

    public function distanceFromStore(float $lat, float $lng): float
    {
        return $this->haversineDistance(
            (float) $this->settings->store_lat,
            (float) $this->settings->store_lng,
            $lat,
            $lng,
        );
    }

    public function isWithinRadius(float $lat, float $lng): bool
    {
        return $this->distanceFromStore($lat, $lng) <= (float) $this->settings->max_radius_km;
    }

    /**
     * Tarif berjenjang: base_fare + (distance × per_km_rate), dibulatkan ke Rp 500 terdekat.
     *
     * @throws OutOfRadiusException
     */
    public function calculateOngkir(float $lat, float $lng): float
    {
        $distance = $this->distanceFromStore($lat, $lng);

        if ($distance > (float) $this->settings->max_radius_km) {
            throw new OutOfRadiusException(sprintf(
                'Lokasi pengantaran %.2f km dari toko, melebihi batas %.2f km.',
                $distance,
                (float) $this->settings->max_radius_km,
            ));
        }

        $raw = (float) $this->settings->base_fare
            + $distance * (float) $this->settings->per_km_rate;

        // Bulatkan ke Rp 500 terdekat (pembulatan ke atas).
        return ceil($raw / 500) * 500;
    }
}
