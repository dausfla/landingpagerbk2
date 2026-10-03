<?php

namespace App\Core;

class Csrf
{
    private const SESSION_KEY = 'rbk_csrf_token';

    /**
     * Get or generate active CSRF token
     */
    public static function getToken(): string
    {
        Auth::startSession();

        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    /**
     * Validate CSRF token from request
     */
    public static function validate(?string $token): bool
    {
        Auth::startSession();

        $sessionToken = $_SESSION[self::SESSION_KEY] ?? '';
        if (empty($sessionToken) || empty($token)) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }
}
