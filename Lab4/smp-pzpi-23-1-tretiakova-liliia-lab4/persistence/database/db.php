<?php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=smp_lab;charset=utf8mb4', 'my_user', 'strong_password');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Помилка підключення: " . $e->getMessage());
}

?>