<?php

// функція для отримання всіх товарів із бд
function getAllProducts(): array {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM products");
    return $stmt->fetchAll();
}

// функція для отримання елементів кошику користувача за токеном кошику
// токен кошику генерується на початку у cart.php
function getCartItems(string $cartToken): array {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT ci.product_id, ci.quantity, p.title, p.price, p.imageUrl
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.id
        WHERE ci.cart_token = :token
    ");
    $stmt->execute(['token' => $cartToken]);
    return $stmt->fetchAll();
}

// функція для додавання товару за його ID певної кількості до кошику та запису в бд
function addToCart(string $cartToken, int $productId, int $quantity): void {
    global $pdo;
    $stmt = $pdo->prepare("
        INSERT INTO cart_items (cart_token, product_id, quantity)
        VALUES (:token, :product_id, :quantity)
        ON DUPLICATE KEY UPDATE quantity = quantity + :quantity
    ");
    $stmt->execute([
        'token' => $cartToken,
        'product_id' => $productId,
        'quantity' => $quantity
    ]);
}

// функція для видалення товару з кошику за його ID
// кошик визначається за його токеном
function removeFromCart(string $cartToken, int $productId): void {
    global $pdo;
    $stmt = $pdo->prepare('DELETE FROM cart_items WHERE cart_token = ? AND product_id = ?');
    $stmt->execute([$cartToken, $productId]);
}

// функція для отримання назви товару за його ID
function getProductNameById(int $productId): string {
    global $pdo;
    $stmt = $pdo->prepare('SELECT title FROM products WHERE id = :productId');
    $stmt->execute(['productId' => $productId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['title'] : '';
}
