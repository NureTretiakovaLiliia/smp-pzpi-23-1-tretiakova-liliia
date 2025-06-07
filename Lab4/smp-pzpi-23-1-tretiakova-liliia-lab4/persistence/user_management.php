<?php

require 'database/db.php';


function userExists(string $username): bool {
    global $pdo;

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);

    return $stmt->fetch() !== false;
}


function loginValid(string $username, string $pwd): bool {
    global $pdo;

    $stmt = $pdo->prepare("SELECT user_password FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user && $pwd === $user['user_password'];
}


function getUserIdByUsername(string $username): int {
    global $pdo;

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->execute([':username' => $username]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && isset($row['id'])) {
        return (int)$row['id'];
    }

    return 0;
}


function registerUser(string $username, string $pwd): bool {
    global $pdo;

    $stmt = $pdo->prepare("INSERT INTO users (username, user_password) VALUES (:username, :password)");

    return $stmt->execute([
        ':username' => $username,
        ':password' => $pwd
    ]);
}


function updateUserProfile(int $userId, array $data): bool {
    global $pdo;

    $sql = "UPDATE users SET first_name = :first_name, last_name = :last_name,
            birth_date = :birth_date, bio = :bio, photo = :photo
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name'],
        'birth_date' => $data['birth_date'],
        'bio' => $data['bio'],
        'photo' => $data['photo'],
        'id' => $userId
    ]);
}

function getUserProfile(int $userId): array {
    global $pdo;

    $stmt = $pdo->prepare("SELECT first_name, last_name, birth_date, bio, photo FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user ?: [
        'first_name' => '',
        'last_name' => '',
        'birth_date' => '',
        'bio' => '',
        'photo' => ''
    ];
}

