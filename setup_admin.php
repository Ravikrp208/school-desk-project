<?php
// setup_admin.php
// This script initializes the database and creates a default admin user.
// Run this by visiting: http://localhost/myschooldesk/setup_admin.php

// 1. Initial connection without database name (to allow creating it)
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$dbname = 'myschooldesk_db';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 2. Create database if it doesn't exist
    echo "Checking database existence...<br>";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database `$dbname` ready.<br>";
    
    // 3. Reconnect with the database selected
    $pdo->exec("USE `$dbname` ");
    
    // 4. Create users table with all required columns
    echo "Checking tables...<br>";
    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `email` VARCHAR(150) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `phone` VARCHAR(20) DEFAULT NULL,
        `role` ENUM('admin', 'school', 'parent') NOT NULL DEFAULT 'parent',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Users table ready.<br>";

    // 5. Check if 'username' column exists in case table was created previously without it
    $stmt = $pdo->query("SHOW COLUMNS FROM `users` LIKE 'username'");
    if (!$stmt->fetch()) {
        echo "Adding missing 'username' column...<br>";
        $pdo->exec("ALTER TABLE `users` ADD COLUMN `username` VARCHAR(50) NOT NULL UNIQUE AFTER `name` ");
    }

    // 6. Insert or update default admin user
    $admin_username = 'admin';
    $admin_password = password_hash('password', PASSWORD_BCRYPT);
    $admin_name = 'Super Admin';
    $admin_email = 'admin@myschooldesk.co.in';

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$admin_username, $admin_email]);
    $userRow = $stmt->fetch();

    if ($userRow) {
        $stmt = $pdo->prepare("UPDATE users SET password = ?, role = 'admin', username = ? WHERE id = ?");
        $stmt->execute([$admin_password, $admin_username, $userRow['id']]);
        echo "Default admin user updated.<br>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (name, username, email, password, role) VALUES (?, ?, ?, ?, 'admin')");
        $stmt->execute([$admin_name, $admin_username, $admin_email, $admin_password]);
        echo "Default admin user created.<br>";
    }

    echo "<h3>Setup completed successfully!</h3>";
    echo "<p>You can now login with:</p>";
    echo "<ul><li><b>Username:</b> admin</li><li><b>Password:</b> password</li></ul>";
    echo "<a href='admin/login.php' style='display:inline-block; padding:10px 20px; background:#4318FF; color:white; text-decoration:none; border-radius:10px; font-weight:bold;'>Go to Admin Login</a>";

} catch (PDOException $e) {
    die("<h3 style='color:red;'>Setup failed: " . $e->getMessage() . "</h3>");
}
?>
