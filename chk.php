<?php
require 'config.php';
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = :user OR email = :user) AND role = 'school' LIMIT 1");
    $stmt->execute(['user' => 'ravikrp208@gmail.com']);
    $user = $stmt->fetch();
    file_put_contents('out.txt', print_r($user, true));
} catch(Exception $e) { file_put_contents('out.txt', "Error: " . $e->getMessage()); }
?>
