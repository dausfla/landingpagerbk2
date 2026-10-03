<?php

namespace App\Models;

use App\Core\DB;

class PackageModel
{
    public static function getPublishedPackages(): array
    {
        $packages = DB::fetchAll("SELECT * FROM packages WHERE is_published = 1 AND deleted_at IS NULL ORDER BY sort_order ASC");
        foreach ($packages as &$pkg) {
            $pkg['specs'] = DB::fetchAll("SELECT label, value FROM package_specs WHERE package_id = ? ORDER BY sort_order ASC", [$pkg['id']]);
            $pkg['suitable_for'] = !empty($pkg['suitable_for']) ? json_decode($pkg['suitable_for'], true) : [];
        }
        return $packages;
    }

    public static function getMinPrice(string $serviceType): int
    {
        $res = DB::fetchOne("SELECT MIN(price_min) AS min_p FROM packages WHERE service_type = ? AND is_published = 1 AND deleted_at IS NULL", [$serviceType]);
        return (int)($res['min_p'] ?? 0);
    }

    public static function getMaxPrice(string $serviceType): int
    {
        $res = DB::fetchOne("SELECT MAX(COALESCE(price_max, price_min)) AS max_p FROM packages WHERE service_type = ? AND is_published = 1 AND deleted_at IS NULL", [$serviceType]);
        return (int)($res['max_p'] ?? 0);
    }
}
