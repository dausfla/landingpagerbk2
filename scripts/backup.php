<?php

/**
 * RBK Database Automated Backup & 14-Day Rotation Script
 * Run via Cron: 0 2 * * * php /path/to/scripts/backup.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->safeLoad();
}

$host = env('DB_HOST', '127.0.0.1');
$port = env('DB_PORT', '3306');
$dbname = env('DB_NAME', 'rbk_db');
$username = env('DB_USER', 'root');
$password = env('DB_PASS', '');

$backupDir = __DIR__ . '/../storage/backups/';
if (!is_dir($backupDir)) {
    @mkdir($backupDir, 0755, true);
}

$date = date('Y-m-d_H-i-s');
$backupFile = $backupDir . "backup_{$dbname}_{$date}.sql.gz";

echo "[BACKUP] Memulai proses backup database '{$dbname}'...\n";

// Execute mysqldump
$passArg = !empty($password) ? "-p" . escapeshellarg($password) : "";
$cmd = "mysqldump -h " . escapeshellarg($host) . " -P " . escapeshellarg($port) . " -u " . escapeshellarg($username) . " {$passArg} " . escapeshellarg($dbname) . " | gzip > " . escapeshellarg($backupFile);

exec($cmd, $output, $returnVar);

if ($returnVar === 0) {
    echo "[SUCCESS] Backup berhasil disimpan ke: {$backupFile}\n";
} else {
    echo "[ERROR] Gagal membuat backup database.\n";
}

// 14-Day Rotation Cleanup
echo "[ROTATION] Membersihkan file backup yang lebih tua dari 14 hari...\n";
$files = glob($backupDir . "backup_*.sql.gz");
$now = time();
$maxAgeSeconds = 14 * 86400; // 14 hari

if (is_array($files)) {
    foreach ($files as $f) {
        if (is_file($f) && ($now - filemtime($f)) > $maxAgeSeconds) {
            @unlink($f);
            echo "   -> Dihapus: " . basename($f) . "\n";
        }
    }
}

echo "[COMPLETED] Proses backup dan rotasi selesai.\n";
