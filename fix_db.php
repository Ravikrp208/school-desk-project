<?php
require_once 'config.php';

try {
    // Check if username column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM `users` LIKE 'username'");
    $columnExists = $stmt->fetch();

    if (!$columnExists) {
        echo "Adding 'username' column to 'users' table...\n";
        $pdo->exec("ALTER TABLE `users` ADD COLUMN `username` VARCHAR(50) NOT NULL UNIQUE AFTER `name` ");
        echo "Column 'username' added successfully.\n";
        
        // Populate username with a slugified version of name or email prefix
        echo "Populating 'username' for existing users...\n";
        $stmt = $pdo->query("SELECT id, email, name FROM users");
        $users = $stmt->fetchAll();
        
        foreach ($users as $user) {
            $username = strtolower(explode('@', $user['email'])[0]); // Simple username from email
            $updateStmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
            $updateStmt->execute([$username, $user['id']]);
            echo "Updated user ID {$user['id']} with username: {$username}\n";
        }
    } else {
        echo "Column 'username' already exists.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
