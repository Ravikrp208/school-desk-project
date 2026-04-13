<?php


// define server database

// define('DB_HOST', 'localhost');
// define('DB_USER', 'u704732842_myschooldesk');
// define('DB_PASS', '9@kkcV?!G');
// define('DB_NAME', 'u704732842_myschooldesk');

// Define database parameters 

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'myschooldesk_db');

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);

    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Disable emulated prepared statements for security and robust typing
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (PDOException $e) {
    // In a real production environment, you would log this error appropriately rather than echoing it to screen.
    die("Database connection failed: " . $e->getMessage());
}
?>