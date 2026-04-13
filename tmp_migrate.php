<?php
require_once 'config.php';
try {
    $pdo->exec("ALTER TABLE schools ADD COLUMN fee_structure TEXT AFTER facilities");
    echo "Column 'fee_structure' added successfully.\n";
} catch (PDOException $e) {
    if ($e->getCode() == '42S21') {
        echo "Column 'fee_structure' already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>
