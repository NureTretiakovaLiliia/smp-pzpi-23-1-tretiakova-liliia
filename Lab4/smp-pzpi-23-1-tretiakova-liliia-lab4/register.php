<?php
session_start();
require_once 'persistence/user_management.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if (userExists($username)) {
        $error = 'Користувач уже існує. Увійдіть <a href="login.php">тут</a>.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Паролі не збігаються.';
    } else {
        $result = registerUser($username, $password);

        if ($result == true) {
            $_SESSION['user_id'] = getUserIdByUsername($username);
            $_SESSION['username'] = $username;
            $_SESSION['login_time'] = time();
        }
        else {
            $error = 'Не вдалося зареєструватися.';
        }

        header("Location: index.php");
        exit;
    }
}

include 'header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow">
                <div class="card-body">
                    <h4 class="card-title mb-4 text-center">Реєстрація</h4>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form method="POST" novalidate>
                        <div class="mb-3">
                            <label for="username" class="form-label">Логін</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Пароль</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Повторіть пароль</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Зареєструватися</button>
                    </form>

                    <p class="text-center mt-3 mb-0">
                        Вже маєте акаунт? <a href="login.php">Увійдіть тут</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
