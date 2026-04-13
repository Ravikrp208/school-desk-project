<?php
// verify_schools_schema.php
require_once 'config.php';

try {
    echo "<h3>Schools Table Verification</h3>";
    
    // Check columns
    $stmt = $pdo->query("SHOW COLUMNS FROM schools");
    $columns = $stmt->fetchAll();
    
    echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    
    foreach ($columns as $column) {
        $color = ($column['Field'] == 'status') ? "style='background:#e6fffa; font-weight:bold;'" : "";
        echo "<tr $color>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Check distinct status values
    echo "<h3>Current Status Values</h3>";
    $statusStmt = $pdo->query("SELECT status, COUNT(*) as count FROM schools GROUP BY status");
    $statuses = $statusStmt->fetchAll();
    
    foreach ($statuses as $s) {
        echo "Status: <b>{$s['status']}</b> - Count: {$s['count']}<br>";
    }

} catch (PDOException $e) {
    echo "<h4 style='color:red;'>Connection Error: " . $e->getMessage() . "</h4>";
}
?>
