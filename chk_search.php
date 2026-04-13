<?php
require_once 'config.php';
$stmt = $pdo->query("SELECT name, city, status, fees_min, facilities FROM schools");
$schools = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Count: " . count($schools) . "\n";
foreach($schools as $s) {
    echo "Name: {$s['name']} | City: {$s['city']} | Status: {$s['status']} | Fee: {$s['fees_min']} | Facs: {$s['facilities']}\n";
}
?>
