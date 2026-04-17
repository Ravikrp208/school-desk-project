<?php
require_once 'config.php';

try {
    $pdo->exec("ALTER TABLE enquiries ADD COLUMN IF NOT EXISTS existing_school VARCHAR(255) AFTER child_class");
    $pdo->exec("ALTER TABLE enquiries ADD COLUMN IF NOT EXISTS passing_year VARCHAR(50) AFTER existing_school");
    $pdo->exec("ALTER TABLE enquiries ADD COLUMN IF NOT EXISTS student_id VARCHAR(100) AFTER passing_year");
    $pdo->exec("ALTER TABLE enquiries ADD COLUMN IF NOT EXISTS govt_id_type VARCHAR(100) AFTER student_id");
    
    echo "Table 'enquiries' updated successfully with new fields!";
} catch (PDOException $e) {
    echo "Error updating table: " . $e->getMessage();
}
?>
