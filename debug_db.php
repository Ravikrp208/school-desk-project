<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

try {
    echo "Connected successfully to " . DB_NAME . "\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM schools");
    $cols = $stmt->fetchAll();
    echo "Columns in schools:\n";
    foreach ($cols as $row) {
        echo "- " . $row['Field'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
