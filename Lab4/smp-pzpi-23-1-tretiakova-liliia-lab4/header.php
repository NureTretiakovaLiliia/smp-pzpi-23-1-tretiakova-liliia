<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Магазин "Весна"</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/minty/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white p-3 mb-4">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3">Магазин "Весна"</h1>
                <nav>
                    <a class="btn btn-outline-light me-2" href="index.php">
                        <i class="bi bi-house"></i>
                    </a>
                    <a class="btn btn-outline-light me-2" href="products.php">Products</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a class="btn btn-outline-light me-2" href="cart.php">
                            <i class="bi bi-basket-fill"></i>
                        </a>
                        <a class="btn btn-outline-light me-2" href="profile.php" class="text-white fs-4">
                            <i class="bi bi-person-circle"></i>
                        </a>
                        <a class="btn btn-outline-danger me-2" href="logout.php">Logout</a>
                    <?php else: ?>
                        <a class="btn btn-light" href="login.php">Login</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>
    <main class="container mb-5">