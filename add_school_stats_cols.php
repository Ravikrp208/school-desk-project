<?php
require_once 'config.php';

try {
    $pdo->exec("ALTER TABLE schools ADD COLUMN IF NOT EXISTS `student_ratio` VARCHAR(50) DEFAULT '1:15' AFTER `teacher_max_qual` ");
    $pdo->exec("ALTER TABLE schools ADD COLUMN IF NOT EXISTS `security_info` VARCHAR(100) DEFAULT 'CCTV 24/7 Monitoring' AFTER `student_ratio` ");
    $pdo->exec("ALTER TABLE schools ADD COLUMN IF NOT EXISTS `curriculum_info` VARCHAR(255) DEFAULT 'CBSE, ICSE, IB, State Board' AFTER `security_info` ");
    $pdo->exec("ALTER TABLE schools ADD COLUMN IF NOT EXISTS `medical_aid` VARCHAR(100) DEFAULT 'Infirmary' AFTER `curriculum_info` ");
    
    echo "Columns added successfully!\n";
} catch (PDOException $e) {
    if ($e->getCode() == '42S21') {
        echo "Columns already exist.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
