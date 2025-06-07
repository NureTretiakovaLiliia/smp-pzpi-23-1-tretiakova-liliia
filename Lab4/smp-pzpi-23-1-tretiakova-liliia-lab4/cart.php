<?php
session_start();

require 'persistence/cart_management.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: page404.php");
}

$cart = getCartItems($_SESSION['user_id']);

echo "<h2>Кошик</h2>";


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_product_id'])) {
    $removeProductId = (int)$_POST['remove_product_id'];
    removeFromCart($_SESSION['user_id'], $removeProductId);
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}
?>

<?php if (!empty($cart)): ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>id</th>
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
                <td><?= number_format($cart_item['price'], 2) ?></td>
                
                
                <td><?= number_format($sum, 2) ?></td>
                <td>
                    <form method="POST" style="margin:0;">
                        <input type="hidden" name="remove_product_id" value="<?= (int)$cart_item['product_id'] ?>">
                        <button type="submit" onclick="return confirm('Видалити цей товар з кошика?')" class="btn btn-danger">Видалити</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align:right;"><strong>Всього:</strong></td>
                <td colspan="2"><strong><?= number_format($total, 2) ?> грн</strong></td>
            </tr>
        </tfoot>
    </table>
<?php else: ?>
    <p>Ваш кошик порожній. <a href="index.php">Перейти до покупок</a></p>
<?php endif; ?>



<?php include 'footer.php'; ?>
