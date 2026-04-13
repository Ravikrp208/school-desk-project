<?php
require 'config.php';
$username = 'ravikrp208@gmail.com';
$password = '+919142605833';

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = :user1 OR email = :user2) AND role = 'school' LIMIT 1");
    $stmt->execute(['user1' => $username, 'user2' => $username]);
    $user = $stmt->fetch();

    $output = "";
    if ($user) {
        $output .= "User found: " . $user['email'] . "\n";
        if (password_verify($password, $user['password'])) {
            $output .= "Login success!\n";
        } else {
            $output .= "Password verification failed.\n";
        }
    } else {
        $output .= "User not found.\n";
    }
    file_put_contents('verify_result.txt', $output);
} catch (PDOException $e) {
    file_put_contents('verify_result.txt', "Error: " . $e->getMessage() . "\n");
}
?>
