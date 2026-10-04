<?php

/**
 * RBK Global Helper Functions
 */

if (!function_exists('str_contains')) {
    /**
     * Polyfill str_contains for PHP < 8.0 compatibility
     */
    function str_contains(string $haystack, string $needle): bool {
        return $needle === '' || strpos($haystack, $needle) !== false;
    }
}

if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool {
        return strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}

if (!function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle): bool {
        return $needle === '' || substr($haystack, -strlen($needle)) === $needle;
    }
}

if (!function_exists('e')) {
    /**
     * Escape HTML output securely (XSS prevention)
     */
    function e(?string $value): string {
        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('env')) {
    /**
     * Get environment variable with fallback
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function env(string $key, $default = null) {
        $val = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        if ($val === false || $val === null || $val === '') {
            return $default;
        }
        if (strtolower($val) === 'true') return true;
        if (strtolower($val) === 'false') return false;
        return $val;
    }
}

if (!function_exists('format_rupiah')) {
    /**
     * Format number to standard Indonesian Rupiah string
     * @param int|float $amount
     * @param bool $withSymbol
     * @return string
     */
    function format_rupiah($amount, bool $withSymbol = true): string {
        $formatted = number_format((float)$amount, 0, ',', '.');
        return $withSymbol ? 'Rp' . $formatted : $formatted;
    }
}

if (!function_exists('format_rupiah_compact')) {
    /**
     * Format large Rupiah amounts into compact strings (e.g., Rp540–600 jt, Rp1,2–1,35 M)
     * @param int|float $min
     * @param int|float|null $max
     * @return string
     */
    function format_rupiah_compact($min, $max = null): string {
        $min = (float)$min;
        $max = $max !== null ? (float)$max : null;

        if ($max === null || $min === $max) {
            if ($min >= 1000000000) {
                return 'Rp' . number_format($min / 1000000000, 2, ',', '.') . ' M';
            }
            if ($min >= 1000000) {
                return 'Rp' . number_format($min / 1000000, 0, ',', '.') . ' jt';
            }
            return format_rupiah($min);
        }

        if ($max >= 1000000000) {
            $minVal = number_format($min / 1000000000, 2, ',', '.');
            $maxVal = number_format($max / 1000000000, 2, ',', '.');
            return "Rp{$minVal}–{$maxVal} M";
        }

        if ($max >= 1000000) {
            $minVal = number_format($min / 1000000, 0, ',', '.');
            $maxVal = number_format($max / 1000000, 0, ',', '.');
            return "Rp{$minVal}–{$maxVal} jt";
        }

        return format_rupiah($min) . ' – ' . format_rupiah($max);
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Get active session CSRF token
     */
    function csrf_token(): string {
        return \App\Core\Csrf::getToken();
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Render hidden CSRF input tag
     */
    function csrf_field(): string {
        return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('url')) {
    /**
     * Generate relative or absolute URL path
     */
    function url(string $path = ''): string {
        $path = ltrim($path, '/');
        return '/' . $path;
    }
}

if (!function_exists('asset')) {
    /**
     * Generate cache-busted asset URL
     */
    function asset(string $path): string {
        $path = ltrim($path, '/');
        $fullPath = __DIR__ . '/../../public/' . $path;
        $v = file_exists($fullPath) ? filemtime($fullPath) : '1.0';
        return '/' . $path . '?v=' . $v;
    }
}
