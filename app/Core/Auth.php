<?php

namespace App\Core;

use App\Models\UserModel;

class Auth
{
    private const SESSION_USER_ID = 'rbk_user_id';
    private const SESSION_USER_DATA = 'rbk_user_data';
    private const SESSION_LAST_ACTIVITY = 'rbk_last_activity';
    private const MAX_IDLE_SECONDS = 7200; // 2 Jam

    /**
     * Start secure PHP session
     */
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'domain'   => '',
                'secure'   => $secure,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_start();
        }

        self::checkIdleTimeout();
    }

    /**
     * Authenticate user with email and password
     */
    public static function attempt(string $email, string $password, Request $request): bool
    {
        self::startSession();

        $ipHash = $request->getIpHash();

        // Check login throttle: max 5 failed attempts per 15 min
        if (UserModel::isThrottled($email, $ipHash)) {
            return false;
        }

        $user = UserModel::findByEmail($email);
        if (!$user || !(int)$user['is_active']) {
            UserModel::recordLoginAttempt($email, $ipHash, false);
            return false;
        }

        if (!password_verify($password, $user['password_hash'])) {
            UserModel::recordLoginAttempt($email, $ipHash, false);
            return false;
        }

        // Login success
        UserModel::recordLoginAttempt($email, $ipHash, true);
        UserModel::updateLastLogin($user['id']);

        // Prevent session fixation
        session_regenerate_id(true);

        $_SESSION[self::SESSION_USER_ID] = $user['id'];
        $_SESSION[self::SESSION_USER_DATA] = [
            'id'                   => $user['id'],
            'name'                 => $user['name'],
            'email'                => $user['email'],
            'role'                 => 'super_admin',
            'must_change_password' => (bool)$user['must_change_password'],
        ];
        $_SESSION[self::SESSION_LAST_ACTIVITY] = time();

        return true;
    }

    /**
     * Check if Super Admin is logged in
     */
    public static function check(): bool
    {
        self::startSession();
        return isset($_SESSION[self::SESSION_USER_ID]) && !empty($_SESSION[self::SESSION_USER_ID]);
    }

    /**
     * Get current authenticated user details
     */
    public static function user(): ?array
    {
        self::startSession();
        return $_SESSION[self::SESSION_USER_DATA] ?? null;
    }

    /**
     * Get current authenticated user ID
     */
    public static function id(): ?int
    {
        self::startSession();
        return $_SESSION[self::SESSION_USER_ID] ?? null;
    }

    /**
     * Logout current user
     */
    public static function logout(): void
    {
        self::startSession();
        unset($_SESSION[self::SESSION_USER_ID], $_SESSION[self::SESSION_USER_DATA], $_SESSION[self::SESSION_LAST_ACTIVITY]);
        session_destroy();
    }

    /**
     * Check idle timeout (2 hours)
     */
    private static function checkIdleTimeout(): void
    {
        if (isset($_SESSION[self::SESSION_LAST_ACTIVITY])) {
            if (time() - $_SESSION[self::SESSION_LAST_ACTIVITY] > self::MAX_IDLE_SECONDS) {
                self::logout();
            } else {
                $_SESSION[self::SESSION_LAST_ACTIVITY] = time();
            }
        }
    }

    /**
     * Hash password using Argon2id with Bcrypt fallback
     */
    public static function hashPassword(string $password): string
    {
        if (defined('PASSWORD_ARGON2ID')) {
            return password_hash($password, PASSWORD_ARGON2ID);
        }
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
}
