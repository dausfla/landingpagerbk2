<?php
require __DIR__ . '/../vendor/autoload.php';

$pdo = App\Core\DB::getConnection();
$stmt = $pdo->prepare("UPDATE sections SET eyebrow = 'ARCHITECTURE & PLANNING JABODETABEK' WHERE `key` = 's2_hero'");
$stmt->execute();
echo "Updated " . $stmt->rowCount() . " row(s) in sections table.\n";

App\Core\Cache::clearAll();
echo "HTML cache cleared.\n";
