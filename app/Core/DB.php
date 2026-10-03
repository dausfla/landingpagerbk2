<?php

namespace App\Core;

use PDO;
use PDOException;

class DB
{
    private static ?PDO $instance = null;

    /**
     * Get singleton PDO connection instance
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $host = env('DB_HOST', '127.0.0.1');
            $port = env('DB_PORT', '3306');
            $dbname = env('DB_NAME', 'rbk_db');
            $username = env('DB_USER', 'root');
            $password = env('DB_PASS', '');

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $username, $password, $options);
            } catch (PDOException $e) {
                if (env('APP_DEBUG', false)) {
                    throw new PDOException("Database Connection Error: " . $e->getMessage(), (int)$e->getCode());
                }
                throw new PDOException("Gagal terhubung ke database.", 500);
            }
        }

        return self::$instance;
    }

    /**
     * Execute SELECT query with prepared statements
     */
    public static function fetchAll(string $sql, array $params = []): array
    {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Execute SELECT single row with prepared statements
     */
    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result === false ? null : $result;
    }

    /**
     * Execute INSERT/UPDATE/DELETE query
     */
    public static function query(string $sql, array $params = []): bool
    {
        $stmt = self::getConnection()->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Execute INSERT and return last inserted ID
     */
    public static function insert(string $sql, array $params = []): string
    {
        $db = self::getConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $db->lastInsertId();
    }
}
