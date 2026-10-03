<?php

namespace App\Models;

use App\Core\DB;

class UserModel
{
    public static function findById(int $id): ?array
    {
        return DB::fetchOne("SELECT * FROM users WHERE id = ? AND deleted_at IS NULL", [$id]);
    }

    public static function findByEmail(string $email): ?array
    {
        return DB::fetchOne("SELECT * FROM users WHERE email = ? AND deleted_at IS NULL", [$email]);
    }

    public static function all(): array
    {
        return DB::fetchAll("SELECT id, name, email, role, is_active, must_change_password, last_login_at, created_at FROM users WHERE deleted_at IS NULL ORDER BY id ASC");
    }

    public static function countActiveSuperAdmins(): int
    {
        $res = DB::fetchOne("SELECT COUNT(*) AS total FROM users WHERE role = 'super_admin' AND is_active = 1 AND deleted_at IS NULL");
        return (int)($res['total'] ?? 0);
    }

    public static function create(array $data): string
    {
        $sql = "INSERT INTO users (name, email, password_hash, role, is_active, must_change_password, created_at)
                VALUES (?, ?, ?, 'super_admin', ?, ?, NOW())";
        return DB::insert($sql, [
            $data['name'],
            $data['email'],
            $data['password_hash'],
            $data['is_active'] ?? 1,
            $data['must_change_password'] ?? 0
        ]);
    }

    public static function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [];

        if (isset($data['name'])) {
            $fields[] = "name = ?";
            $params[] = $data['name'];
        }
        if (isset($data['email'])) {
            $fields[] = "email = ?";
            $params[] = $data['email'];
        }
        if (isset($data['password_hash'])) {
            $fields[] = "password_hash = ?";
            $params[] = $data['password_hash'];
        }
        if (isset($data['is_active'])) {
            $fields[] = "is_active = ?";
            $params[] = $data['is_active'];
        }
        if (isset($data['must_change_password'])) {
            $fields[] = "must_change_password = ?";
            $params[] = $data['must_change_password'];
        }

        if (empty($fields)) {
            return false;
        }

        $params[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
        return DB::query($sql, $params);
    }

    public static function softDelete(int $id): bool
    {
        return DB::query("UPDATE users SET deleted_at = NOW(), is_active = 0 WHERE id = ?", [$id]);
    }

    public static function updateLastLogin(int $id): void
    {
        DB::query("UPDATE users SET last_login_at = NOW() WHERE id = ?", [$id]);
    }

    /**
     * Record login attempt for throttle check
     */
    public static function recordLoginAttempt(string $email, string $ipHash, bool $success): void
    {
        DB::query(
            "INSERT INTO login_attempts (email, ip_hash, success, attempted_at) VALUES (?, ?, ?, NOW())",
            [$email, $ipHash, $success ? 1 : 0]
        );
    }

    /**
     * Check if login is throttled: 5 failed attempts per 15 minutes per email + IP
     */
    public static function isThrottled(string $email, string $ipHash): bool
    {
        $res = DB::fetchOne(
            "SELECT COUNT(*) AS total FROM login_attempts WHERE email = ? AND ip_hash = ? AND success = 0 AND attempted_at >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)",
            [$email, $ipHash]
        );
        return ((int)($res['total'] ?? 0)) >= 5;
    }
}
