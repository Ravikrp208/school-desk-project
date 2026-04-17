<?php
// fix_database.php
require_once 'config.php';

try {
    $pdo->exec("ALTER TABLE schools ADD COLUMN student_ratio VARCHAR(50) DEFAULT '1:15' AFTER teacher_max_qual");
    echo "Column student_ratio added.<br>";
} catch (PDOException $e) {
    echo "student_ratio: " . $e->getMessage() . "<br>";
}

try {
    $pdo->exec("ALTER TABLE schools ADD COLUMN security_info VARCHAR(100) DEFAULT 'CCTV 24/7 Monitoring' AFTER student_ratio");
    echo "Column security_info added.<br>";
} catch (PDOException $e) {
    echo "security_info: " . $e->getMessage() . "<br>";
}

try {
    $pdo->exec("ALTER TABLE schools ADD COLUMN curriculum_info VARCHAR(255) DEFAULT 'CBSE, ICSE, IB, State Board' AFTER security_info");
    echo "Column curriculum_info added.<br>";
} catch (PDOException $e) {
    echo "curriculum_info: " . $e->getMessage() . "<br>";
}

try {
    $pdo->exec("ALTER TABLE schools ADD COLUMN medical_aid VARCHAR(100) DEFAULT 'Infirmary' AFTER curriculum_info");
    echo "Column medical_aid added.<br>";
} catch (PDOException $e) {
    echo "medical_aid: " . $e->getMessage() . "<br>";
}

echo "Database fix script finished.";
?>