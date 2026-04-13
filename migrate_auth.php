<?php
require_once 'config.php';

try {
    // 1. Add username column if it doesn't exist
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'username'");
    $exists = $stmt->fetch();

    if (!$exists) {
        $pdo->exec("ALTER TABLE users ADD COLUMN username VARCHAR(50) NOT NULL UNIQUE AFTER name");
        echo "Column 'username' added.\n";
    } else {
        echo "Column 'username' already exists.\n";
    }

    // 2. Update default admin user
    $stmt = $pdo->prepare("UPDATE users SET username = 'admin' WHERE email = 'admin@myschooldesk.co.in' AND role = 'admin'");
    $stmt->execute();
    
    // 3. Update other users if needed (using email prefix as default username)
    $users = $pdo->query("SELECT id, email FROM users WHERE username = '' OR username IS NULL")->fetchAll();
    foreach ($users as $user) {
        $username = explode('@', $user['email'])[0];
        $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
        $stmt->execute([$username, $user['id']]);
    }

    echo "Database migration completed successfully.";
} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage());
}
?>
