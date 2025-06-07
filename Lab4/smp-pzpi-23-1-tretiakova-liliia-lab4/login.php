<?php
session_start();

require_once 'persistence/user_management.php'; 


$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (loginValid($username, $password)) {
        $_SESSION['user_id'] = getUserIdByUsername($username);
        $_SESSION['username'] = $username;
        $_SESSION['login_time'] = time();
        header("Location: index.php");
    }
    else {
        $error = 'Невірний логін або пароль.';
    }
}

include 'header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-body">
                    <h4 class="card-title mb-4 text-center">Вхід до акаунту</h4>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Логін</label>
                            <input type="text" class="form-control" id="username" name="username" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Пароль</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Увійти</button>
                    </form>
                    <p class="text-center mt-3 mb-0">
                        Ще не маєте акаунту? <a href="register.php">Зареєструйтеся тут</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
