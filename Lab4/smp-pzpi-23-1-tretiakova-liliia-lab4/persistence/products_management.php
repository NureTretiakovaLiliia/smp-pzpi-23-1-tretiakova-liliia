<?php

require 'persistence/database/db.php';


function getAllProducts(): array {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM products");
    return $stmt->fetchAll();
}