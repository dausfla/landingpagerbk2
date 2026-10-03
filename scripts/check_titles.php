<?php
require __DIR__ . '/../vendor/autoload.php';

$pdo = App\Core\DB::getConnection();
$stmt = $pdo->query("SELECT id, `key`, title FROM sections");
$sections = $stmt->fetchAll();

foreach ($sections as $s) {
    echo "ID {$s['id']} [{$s['key']}]: {$s['title']}\n";
}
