<?php
require_once 'config.php';

function columnExists($pdo, $table, $column)
{
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        return (bool) $stmt->fetch();
    } catch (Exception $e) {
        return false;
    }
}

$columnsNeeded = [
    'student_ratio' => "VARCHAR(50) DEFAULT '1:15' AFTER `teacher_max_qual` ",
    'security_info' => "VARCHAR(100) DEFAULT 'CCTV 24/7 Monitoring' AFTER `student_ratio` ",
    'curriculum_info' => "VARCHAR(255) DEFAULT 'CBSE, ICSE, IB, State Board' AFTER `security_info` ",
    'medical_aid' => "VARCHAR(100) DEFAULT 'Infirmary' AFTER `curriculum_info` "
];

echo "Checking schools table structure...\n";

foreach ($columnsNeeded as $col => $def) {
    if (!columnExists($pdo, 'schools', $col)) {
        echo "Adding column $col...\n";
        try {
            $pdo->exec("ALTER TABLE `schools` ADD COLUMN `$col` $def");
            echo "Column $col added.\n";
        } catch (PDOException $e) {
            echo "Error adding column $col: " . $e->getMessage() . "\n";
        }
    } else {
        echo "Column $col already exists.\n";
    }
}

echo "Verification complete.\n";
?>