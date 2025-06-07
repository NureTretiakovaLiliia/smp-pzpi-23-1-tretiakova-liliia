<?php
session_start();

$errors = [];
$success = '';
require_once 'persistence/user_management.php';

$profile = [
    'first_name' => '',
    'last_name' => '',
    'birth_date' => '',
    'bio' => '',
    'photo' => ''
];

if (isset($_SESSION['user_id'])) {
    $profile = getUserProfile((int)$_SESSION['user_id']);
} else {
    header('Location: page404.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profile['first_name'] = trim($_POST['first_name'] ?? '');
    $profile['last_name'] = trim($_POST['last_name'] ?? '');
    $profile['birth_date'] = $_POST['birth_date'] ?? '';
    $profile['bio'] = trim($_POST['bio'] ?? '');

    if ($profile['first_name'] === '' || strlen($profile['first_name']) < 2) {
        $errors[] = 'Ім’я повинно містити щонайменше 2 символи.';
    }

    if ($profile['last_name'] === '' || strlen($profile['last_name']) < 2) {
        $errors[] = 'Прізвище повинно містити щонайменше 2 символи.';
    }

    if ($profile['birth_date'] === '') {
        $errors[] = 'Дата народження обов’язкова.';
    } else {
        $birth = DateTime::createFromFormat('Y-m-d', $profile['birth_date']);
        $today = new DateTime();
        $age = $birth ? $birth->diff($today)->y : 0;
        if (!$birth || $age < 16) {
            $errors[] = 'Користувач повинен бути старше 16 років.';
        }
    }

    if (strlen($profile['bio']) < 50) {
        $errors[] = 'Інформація про себе повинна містити щонайменше 50 символів.';
    }

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['photo']['tmp_name'];
        $fileName = basename($_FILES['photo']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($fileExt, $allowed)) {
            $errors[] = 'Дозволені лише JPG, JPEG, PNG, GIF.';
        } else {
            $newFileName = 'uploads/user_' . time() . '.' . $fileExt;
            if (!is_dir('uploads')) mkdir('uploads', 0777, true);

            if (move_uploaded_file($fileTmp, $newFileName)) {
                $profile['photo'] = $newFileName;
            } else {
                $errors[] = 'Не вдалося зберегти фото.';
            }
        }
    }

    if (empty($errors)) {
    
        if (isset($_SESSION['user_id'])) {
            updateUserProfile((int)$_SESSION['user_id'], $profile);
        }
    
        $success = 'Профіль оновлено.';
    }
    
}

?>

<?php include 'header.php'; ?>

<div class="container">
    <h2>Профіль користувача</h2>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <div style="display: flex; align-items: flex-start; gap: 20px;">
        <div style="flex-shrink: 0;">
            <?php if (!empty($profile['photo'])): ?>
                <img src="<?= htmlspecialchars($profile['photo']) ?>" alt="Фото" style="max-width: 150px; max-height: 200px; border-radius: 8px; object-fit: cover;">
            <?php else: ?>
                <div style="width: 150px; height: 200px; background: #eee; display: flex; align-items: center; justify-content: center; color: #999; border-radius: 8px;">
                    Фото відсутнє
                </div>
            <?php endif; ?>
        </div>


        <form method="POST" enctype="multipart/form-data" style="flex-grow: 1;">
            <div class="mb-3">
                <label for="first_name" class="form-label">Ім'я:</label>
                <input type="text" class="form-control" name="first_name" id="first_name" value="<?= htmlspecialchars($profile['first_name']) ?>">
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">Прізвище:</label>
                <input type="text" class="form-control" name="last_name" id="last_name" value="<?= htmlspecialchars($profile['last_name']) ?>">
            </div>
            <div class="mb-3">
                <label for="birth_date" class="form-label">Дата народження:</label>
                <input type="date" class="form-control" name="birth_date" id="birth_date" value="<?= htmlspecialchars($profile['birth_date']) ?>">
            </div>
            <div class="mb-3">
                <label for="bio" class="form-label">Інформація про себе:</label>
                <textarea class="form-control" name="bio" id="bio" rows="3"><?= htmlspecialchars($profile['bio']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="photo" class="form-label">Завантажити нове фото:</label>
                <input type="file" class="form-control" name="photo" id="photo">
            </div>
            <button type="submit" class="btn btn-primary">Зберегти</button>
        </form>
    </div>
</div>


<?php include 'footer.php'; ?>
