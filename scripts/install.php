<?php

/**
 * RBK Installation & Database Migration Script
 * Run via CLI: php scripts/install.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Auth;

if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->safeLoad();
}

$host = env('DB_HOST', '127.0.0.1');
$port = env('DB_PORT', '3306');
$dbname = env('DB_NAME', 'rbk_db');
$username = env('DB_USER', 'root');
$password = env('DB_PASS', '');

echo "========================================================\n";
echo "RBK STUDIO x RBK KONSTRUKSI — SYSTEM INSTALLATION\n";
echo "========================================================\n\n";

try {
    // 1. Connect without dbname to create database if missing
    $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "[1/4] Membuat database '{$dbname}' jika belum ada...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$dbname}`");

    // 2. Run schema.sql
    echo "[2/4] Eksekusi file database/schema.sql...\n";
    $schemaSql = file_get_contents(__DIR__ . '/../database/schema.sql');
    $pdo->exec($schemaSql);

    // 3. Run seed.sql
    echo "[3/4] Eksekusi file database/seed.sql...\n";
    $seedSql = file_get_contents(__DIR__ . '/../database/seed.sql');
    $pdo->exec($seedSql);

    // 4. Create Initial Super Admin
    echo "[4/4] Membuat akun Super Admin awal...\n";
    $adminEmail = env('INITIAL_ADMIN_EMAIL', 'admin@rancangbangunkreasi.id');
    $adminName = env('INITIAL_ADMIN_NAME', 'Super Admin RBK');
    
    // Generate secure random password
    $randomPassword = bin2hex(random_bytes(6)); // 12 chars
    $hash = Auth::hashPassword($randomPassword);

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$adminEmail]);
    if ($stmt->fetch()) {
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, is_active = 1, must_change_password = 1 WHERE email = ?");
        $stmt->execute([$hash, $adminEmail]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role, is_active, must_change_password, created_at) VALUES (?, ?, ?, 'super_admin', 1, 1, NOW())");
        $stmt->execute([$adminName, $adminEmail, $hash]);
    }

    echo "\n--------------------------------------------------------\n";
    echo "SUCCESS: INSTALASI SISTEM BERHASIL!\n";
    echo "--------------------------------------------------------\n";
    echo "URL Admin Login : " . env('APP_URL', 'http://localhost:8000') . "/admin/login\n";
    echo "Email Super Admin: {$adminEmail}\n";
    echo "Password Sekali : {$randomPassword}\n";
    echo "--------------------------------------------------------\n";
    echo "* SIMPAN PASSWORD DI ATAS! Password tidak akan ditampilkan lagi.\n";
    echo "========================================================\n\n";

} catch (Exception $e) {
    echo "\n[ERROR] Instalasi Gagal: " . $e->getMessage() . "\n";
    exit(1);
}
