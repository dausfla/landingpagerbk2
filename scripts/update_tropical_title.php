<?php
require __DIR__ . '/../vendor/autoload.php';

$pdo = App\Core\DB::getConnection();
$newTitle = '*Desain rumah tropis* yang tangguh di cuaca Bogor';

$stmt = $pdo->prepare("UPDATE sections SET title = ? WHERE `key` = 's7_tropical_bogor'");
$stmt->execute([$newTitle]);
echo "Updated s7_tropical_bogor title to: {$newTitle}\n";

App\Core\Cache::clearAll();
echo "HTML Cache cleared.\n";
