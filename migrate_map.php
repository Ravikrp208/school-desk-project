<?php
require_once 'config.php';
try {
    $pdo->exec("ALTER TABLE schools ADD COLUMN map_location TEXT AFTER photos");
    echo "SUCCESS: map_location column added to schools table.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "INFO: map_location column already exists.\n";
    } else {
        echo "ERROR: " . $e->getMessage() . "\n";
    }
}
?>
