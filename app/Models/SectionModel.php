<?php

namespace App\Models;

use App\Core\DB;

class SectionModel
{
    /**
     * Get all visible sections ordered by sort_order
     */
    public static function getVisibleSections(): array
    {
        $rows = DB::fetchAll("SELECT * FROM sections WHERE is_visible = 1 AND deleted_at IS NULL ORDER BY sort_order ASC");
        $map = [];
        foreach ($rows as $row) {
            $map[$row['key']] = $row;
        }
        return $map;
    }

    /**
     * Get all sections for admin dashboard management
     */
    public static function getAll(): array
    {
        return DB::fetchAll("SELECT * FROM sections WHERE deleted_at IS NULL ORDER BY sort_order ASC");
    }

    /**
     * Update section content
     */
    public static function updateByKey(string $key, array $data): bool
    {
        $fields = [];
        $params = [];

        foreach (['eyebrow', 'title', 'subtitle', 'body', 'cta_label', 'cta_target', 'image_media_id', 'is_visible', 'sort_order'] as $col) {
            if (isset($data[$col])) {
                $fields[] = "`{$col}` = ?";
                $params[] = $data[$col];
            }
        }

        if (empty($fields)) return false;

        $params[] = $key;
        $sql = "UPDATE sections SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE `key` = ?";
        return DB::query($sql, $params);
    }
}
