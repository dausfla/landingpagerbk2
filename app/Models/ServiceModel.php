<?php

namespace App\Models;

use App\Core\DB;

class ServiceModel
{
    public static function getPublishedServices(): array
    {
        $services = DB::fetchAll("SELECT * FROM services WHERE is_published = 1 AND deleted_at IS NULL ORDER BY sort_order ASC");
        foreach ($services as &$svc) {
            $svc['items'] = DB::fetchAll("SELECT * FROM service_items WHERE service_id = ? ORDER BY sort_order ASC", [$svc['id']]);
        }
        return $services;
    }
}
