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
include 'header.php';

$cart = getCartItems($token);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_product_id'])) {
    $removeProductId = (int)$_POST['remove_product_id'];
    removeFromCart($token, $removeProductId);
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}
?>

<h2>Кошик</h2>

<?php if (!empty($cart)): ?>
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Назва</th>
                <th>Кількість</th>
                <th>Ціна</th>
                <th>Сума</th>
                <th>Дія</th>
            </tr>
        </thead>
        <tbody>
        <?php 
            $total = 0;
            foreach ($cart as $cart_item): 
                $sum = $cart_item['price'] * $cart_item['quantity'];
                $total += $sum;
        ?>
            <tr>
                <td><?= (int)$cart_item['product_id']?></td>
                <td><?= htmlspecialchars($cart_item['title']) ?></td>
                <td><?= (int)$cart_item['quantity'] ?></td>
                <td><?= number_format($cart_item['price'], 2) ?> грн</td>
                <td><?= number_format($sum, 2) ?> грн</td>
                <td>
                    <form method="POST" onsubmit="return confirm('Видалити цей товар з кошика?')" class="d-inline">
                        <input type="hidden" name="remove_product_id" value="<?= (int)$cart_item['product_id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Видалити</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-end"><strong>Всього:</strong></td>
                <td colspan="2"><strong><?= number_format($total, 2) ?> грн</strong></td>
            </tr>
        </tfoot>
    </table>
<?php else: ?>
    <div class="alert alert-info">Ваш кошик порожній. <a href="index.php" class="alert-link">Перейти до покупок</a></div>
<?php endif; ?>

<?php include 'footer.php'; ?>
