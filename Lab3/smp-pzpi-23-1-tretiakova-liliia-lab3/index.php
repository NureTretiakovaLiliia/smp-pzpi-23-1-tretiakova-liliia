<?php
session_start();

if (!isset($_COOKIE['cart_token'])) {
    $token = bin2hex(random_bytes(16)); 
    setcookie('cart_token', $token, time() + 60 * 60 * 24 * 30);
} else {
    $token = $_COOKIE['cart_token'];
}

require 'database/db.php';
require 'database/crud.php';

$products = getAllProducts();
$quantities = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['quantity']) && is_array($_POST['quantity'])) {
        foreach ($_POST['quantity'] as $productId => $qty) {
            $qty = trim($qty);
            if (!preg_match('/^\d+$/', $qty)) {
                $product_name = getProductNameById($productId);
                $errors[] = "Кількість для товару $product_name має бути цілим невід’ємним числом.";
            } else {
                $quantities[$productId] = (int)$qty;
            }
        }
    } else {
        $errors[] = "Не вказано жодної кількості.";
    }

    if (empty($errors)) {
        foreach ($quantities as $productId => $qty) {
            if ($qty > 0) {
                addToCart($token, $productId, $qty);
            }
        }
        header("Location: cart.php");
        exit;
    }
}

include 'header.php';

if (!empty($errors)) {
    echo '<div class="alert alert-danger"><ul>';
    foreach ($errors as $error) {
        echo '<li>' . htmlspecialchars($error) . '</li>';
    }
    echo '</ul></div>';
}
?>

<h2>Список товарів</h2>

<form method="POST" novalidate>
    <div class="list-group">
        <?php foreach ($products as $product): 
            $val = isset($quantities[$product['id']]) ? $quantities[$product['id']] : 0;
        ?>
        <div class="list-group-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <img src="images/<?= htmlspecialchars($product['imageUrl'] ?? 'placeholder.jpg') ?>" 
                 alt="<?= htmlspecialchars($product['title']) ?>" 
                 class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">

            <div class="flex-grow-1">
                <h5 class="mb-1"><?= htmlspecialchars($product['title']) ?></h5>
                <p class="mb-1">Ціна: <?= number_format($product['price'], 2) ?> грн</p>
            </div>

            <div style="min-width: 120px;">
                <label class="form-label mb-0">
                    Кількість:
                    <input type="number" name="quantity[<?= $product['id'] ?>]" 
                           value="<?= htmlspecialchars($val) ?>" min="0" step="1" 
                           class="form-control form-control-sm">
                </label>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-success btn-lg">Купити</button>
    </div>
</form>

<?php include 'footer.php'; ?>
