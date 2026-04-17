<?php
require_once 'config.php';
try {
    $pdo->exec("ALTER TABLE schools ADD COLUMN IF NOT EXISTS reg_certificate VARCHAR(255) DEFAULT NULL AFTER map_location");
    echo "Column 'reg_certificate' added successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
