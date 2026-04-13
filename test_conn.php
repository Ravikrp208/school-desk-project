<?php
require_once 'config.php';
echo "Connection successful!\n";
$stmt = $pdo->query("SELECT DATABASE()");
echo "Database: " . $stmt->fetchColumn() . "\n";
?>
