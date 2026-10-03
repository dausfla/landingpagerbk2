<?php

namespace App\Models;

use App\Core\DB;

class WaClickModel
{
    public static function record(array $data): void
    {
        $sql = "INSERT INTO wa_clicks (cta_location, utm_source, utm_medium, utm_campaign, page_url, device, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())";

        DB::query($sql, [
            $data['cta_location'] ?? 'floating_wa',
            $data['utm_source'] ?? null,
            $data['utm_medium'] ?? null,
            $data['utm_campaign'] ?? null,
            $data['page_url'] ?? '/',
            $data['device'] ?? 'desktop'
        ]);
    }
}
