<?php
require __DIR__ . '/../vendor/autoload.php';

$pdo = App\Core\DB::getConnection();
$newTitle = 'Jasa Arsitek & Kontraktor *Terbaik & Terlengkap* untuk Mewujudkan Bangunan Impian Anda';
$stmt = $pdo->prepare("UPDATE sections SET title = ? WHERE `key` = 's2_hero'");
$stmt->execute([$newTitle]);
echo "Updated s2_hero title: {$newTitle}\n";

App\Core\Cache::clearAll();
echo "HTML Cache cleared.\n";
