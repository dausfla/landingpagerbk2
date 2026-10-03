<?php

namespace App\Models;

use App\Core\DB;
use App\Core\Cache;

class SettingModel
{
    /**
     * Get value of single setting key
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $res = DB::fetchOne("SELECT value FROM settings WHERE `key` = ?", [$key]);
        return $res['value'] ?? $default;
    }

    /**
     * Get all settings as key => value map
     */
    public static function getAll(): array
    {
        $rows = DB::fetchAll("SELECT `key`, value, `group` FROM settings");
        $map = [];
        foreach ($rows as $row) {
            $map[$row['key']] = $row['value'];
        }
        return $map;
    }

    /**
     * Get settings grouped by group name
     */
    public static function getByGroup(string $group): array
    {
        $rows = DB::fetchAll("SELECT `key`, value FROM settings WHERE `group` = ?", [$group]);
        $map = [];
        foreach ($rows as $row) {
            $map[$row['key']] = $row['value'];
        }
        return $map;
    }

    /**
     * Set or update setting value
     */
    public static function set(string $key, ?string $value, string $group = 'general', ?int $userId = null): void
    {
        $exists = DB::fetchOne("SELECT `key` FROM settings WHERE `key` = ?", [$key]);
        if ($exists) {
            DB::query("UPDATE settings SET value = ?, `group` = ?, updated_by = ?, updated_at = NOW() WHERE `key` = ?", [$value, $group, $userId, $key]);
        } else {
            DB::query("INSERT INTO settings (`key`, value, `group`, updated_by, updated_at) VALUES (?, ?, ?, ?, NOW())", [$key, $value, $group, $userId]);
        }

        // Auto invalidate landing page cache
        Cache::clearAll();
    }

    /**
     * Save multiple settings from array
     */
    public static function setMany(array $settings, string $group = 'general', ?int $userId = null): void
    {
        foreach ($settings as $key => $value) {
            self::set($key, (string)$value, $group, $userId);
        }
    }
}
