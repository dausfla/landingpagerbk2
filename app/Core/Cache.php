<?php

namespace App\Core;

class Cache
{
    private static string $cacheDir = __DIR__ . '/../../storage/cache/';

    /**
     * Get cached HTML content if valid
     */
    public static function get(string $key, int $ttlSeconds = 3600): ?string
    {
        $filePath = self::$cacheDir . md5($key) . '.html';

        if (file_exists($filePath) && (time() - filemtime($filePath)) < $ttlSeconds) {
            return file_get_contents($filePath);
        }

        return null;
    }

    /**
     * Store HTML content to cache file
     */
    public static function set(string $key, string $content): void
    {
        if (!is_dir(self::$cacheDir)) {
            @mkdir(self::$cacheDir, 0755, true);
        }

        $filePath = self::$cacheDir . md5($key) . '.html';
        @file_put_contents($filePath, $content);
    }

    /**
     * Clear all cached files (Cache Invalidation on Admin Updates)
     */
    public static function clearAll(): void
    {
        if (!is_dir(self::$cacheDir)) {
            return;
        }

        $files = glob(self::$cacheDir . '*.html');
        if (is_array($files)) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }
    }
}
