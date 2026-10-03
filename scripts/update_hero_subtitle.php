<?php
require __DIR__ . '/../vendor/autoload.php';

$pdo = App\Core\DB::getConnection();
$newSubtitle = '<strong>Rencanakan bersama RBK Studio</strong>, <strong>bangun bersama RBK Konstruksi</strong>. Satu tim, satu alur, dari konsep hingga serah terima.';

$stmt = $pdo->prepare("UPDATE sections SET subtitle = ? WHERE `key` = 's2_hero'");
$stmt->execute([$newSubtitle]);
echo "Updated s2_hero subtitle: {$newSubtitle}\n";

App\Core\Cache::clearAll();
echo "HTML Cache cleared.\n";
