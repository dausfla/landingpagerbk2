<?php

namespace App\Models;

use App\Core\DB;

class ActivityLogModel
{
    public static function log(string $action, string $entity, ?int $entityId = null, ?array $before = null, ?array $after = null, ?int $userId = null, string $ipHash = ''): void
    {
        $sql = "INSERT INTO activity_logs (user_id, action, entity, entity_id, before_json, after_json, ip_hash, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

        DB::query($sql, [
            $userId,
            $action,
            $entity,
            $entityId,
            $before !== null ? json_encode($before, JSON_UNESCAPED_UNICODE) : null,
            $after !== null ? json_encode($after, JSON_UNESCAPED_UNICODE) : null,
            $ipHash
        ]);
    }

    public static function getLatest(int $limit = 50): array
    {
        $sql = "SELECT l.*, u.name AS user_name, u.email AS user_email
                FROM activity_logs l
                LEFT JOIN users u ON l.user_id = u.id
                ORDER BY l.created_at DESC
                LIMIT ?";
        return DB::fetchAll($sql, [$limit]);
    }
}
