<?php

namespace App\Middleware;

use App\Core\DB;
use App\Core\Request;
use App\Core\Response;

class RateLimitMiddleware
{
    public function handle(Request $request): bool
    {
        $ipHash = $request->getIpHash();
        $action = 'submit_lead';

        // Check 10-minute threshold (max 5)
        $tenMinCount = DB::fetchOne(
            "SELECT COUNT(*) AS total FROM rate_limits WHERE ip_hash = ? AND action = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)",
            [$ipHash, $action]
        )['total'] ?? 0;

        if ($tenMinCount >= 5) {
            return self::blockResponse($request, "Terlalu banyak permintaan. Silakan tunggu 10 menit sebelum mencoba kembali.");
        }

        // Check 24-hour threshold (max 20)
        $dayCount = DB::fetchOne(
            "SELECT COUNT(*) AS total FROM rate_limits WHERE ip_hash = ? AND action = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)",
            [$ipHash, $action]
        )['total'] ?? 0;

        if ($dayCount >= 20) {
            return self::blockResponse($request, "Batas harian tercapai. Silakan coba kembali besok.");
        }

        // Record current request attempt
        DB::query("INSERT INTO rate_limits (ip_hash, action, created_at) VALUES (?, ?, NOW())", [$ipHash, $action]);

        return true;
    }

    private static function blockResponse(Request $request, string $message): bool
    {
        if ($request->isAjax()) {
            Response::json(['success' => false, 'message' => $message], 429);
            return false;
        }

        Response::html($message, 429);
        return false;
    }
}
