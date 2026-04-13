<?php
require_once 'config.php';
$stmt = $pdo->query('DESCRIBE schools');
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
?>
