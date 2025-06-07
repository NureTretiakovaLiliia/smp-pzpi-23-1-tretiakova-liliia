<?php

require 'persistence/database/db.php';

function getCartItems(int $user_id): array {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT ci.product_id, ci.quantity, p.title, p.price, p.imageUrl
        FROM cart_items_authorised ci
        JOIN products p ON ci.product_id = p.id
        WHERE ci.user_id = :user_id
    ");
    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetchAll();
}

function addToCart(int $user_id, int $productId, int $quantity): void {
    global $pdo;
    $stmt = $pdo->prepare("
        INSERT INTO cart_items_authorised (user_id, product_id, quantity)
        VALUES (:user_id, :product_id, :quantity)
        ON DUPLICATE KEY UPDATE quantity = quantity + :quantity
    ");
    $stmt->execute([
        'user_id' => $user_id,
        'product_id' => $productId,
        'quantity' => $quantity
    ]);
}

function clearCart(int $user_id): void {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM cart_items_authorised WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
}

function removeFromCart(int $user_id, int $productId): void {
    global $pdo;
    $stmt = $pdo->prepare('DELETE FROM cart_items_authorised WHERE user_id = ? AND product_id = ?');
    $stmt->execute([$user_id, $productId]);
}
