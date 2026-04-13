<?php
// verify_schema.php
require_once 'config.php';

try {
    echo "<h3>System Verification</h3>";

    // Check connection
    echo "Database: " . DB_NAME . "<br>";

    // Check columns
    $stmt = $pdo->query("SHOW COLUMNS FROM users");
    $columns = $stmt->fetchAll();

    echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";

    $usernameExists = false;
    foreach ($columns as $column) {
        $color = ($column['Field'] == 'username') ? "style='background:#e6fffa; font-weight:bold;'" : "";
        if ($column['Field'] == 'username')
            $usernameExists = true;

        echo "<tr $color>";
        echo "<td>{$column['Field']}</td>";
        echo "<td>{$column['Type']}</td>";
        echo "<td>{$column['Null']}</td>";
        echo "<td>{$column['Key']}</td>";
        echo "</tr>";
    }
    echo "</table>";

    if ($usernameExists) {
        echo "<h4 style='color:green;'> 'username' column is present!</h4>";
        echo "<p>You should now be able to login.</p>";
    } else {
        echo "<h4 style='color:red;'> 'username' column is STILL MISSING.</h4>";
        echo "<p>Please run <a href='setup_admin.php'>setup_admin.php</a> first.</p>";
    }

} catch (PDOException $e) {
    echo "<h4 style='color:red;'>Connection Error: " . $e->getMessage() . "</h4>";
}
?>