<?php
// Настройки подключения к базе данных
$host = 'localhost';
$db = 'blog_db';
$user = 'root';
$pass = 'root';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Получение текущего пользователя (предполагается, что он уже авторизован)
session_start();
$author_id = $_SESSION['user_id'] ?? null;
if (!$author_id) {
    echo "<p>Авторизуйтесь, чтобы смотреть раздел.</p>";
    exit;
}

// Получение данных профиля пользователя
$stmt = $pdo->prepare("SELECT description, photo FROM profiles WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $author_id);
$stmt->execute();
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT username, role FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $author_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$username = $user['username'] ?? 'Имя пользователя';
$role = $user['role'] ?? 'user';

$description = $profile['description'] ?? 'Описание отсутствует';
$photo = $profile['photo'] ?? 'uploads/default.png';

// Получение постов пользователя
$stmt = $pdo->prepare("SELECT id, title, content, image, created_at FROM news WHERE author_id = :author_id ORDER BY created_at DESC");
$stmt->bindParam(':author_id', $author_id);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Обработка загрузки нового фото
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['photo']['name']);

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
            $stmt = $pdo->prepare("UPDATE profiles SET photo = :photo WHERE user_id = :user_id");
            $stmt->bindParam(':photo', $uploadFile);
            $stmt->bindParam(':user_id', $author_id);
            $stmt->execute();
        }
    }

    if (isset($_POST['description'])) {
        $newDescription = $_POST['description'];
        $stmt = $pdo->prepare("UPDATE profiles SET description = :description WHERE user_id = :user_id");
        $stmt->bindParam(':description', $newDescription);
        $stmt->bindParam(':user_id', $author_id);
        $stmt->execute();
    }
}
?>

<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль пользователя</title>
    <link rel="stylesheet" href="css/styles.css">
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const profilePhoto = document.querySelector(".user-profile-photo");

            if (profilePhoto) {
                profilePhoto.addEventListener("dblclick", () => {
                    const fileInput = document.getElementById("photo");
                    if (fileInput) {
                        fileInput.click();
                    }
                });
            }

            document.getElementById("photo").addEventListener("change", (event) => {
                const file = event.target.files[0];
                if (file) {
                    const formData = new FormData();
                    formData.append("photo", file);

                    fetch("profile.php", {
                        method: "POST",
                        body: formData,
                    })
                        .then(() => location.reload())
                        .catch((error) => console.error("Ошибка загрузки файла:", error));
                }
            });

            document.querySelector(".profile-description").addEventListener("dblclick", () => {
                const descriptionText = document.querySelector(".profile-description");
                const input = document.createElement("input");
                input.type = "text";
                input.value = descriptionText.textContent;
                descriptionText.innerHTML = "";
                descriptionText.appendChild(input);
                input.focus();

                input.addEventListener("blur", () => {
                    const newDescription = input.value;
                    const formData = new FormData();
                    formData.append("description", newDescription);

                    fetch("profile.php", {
                        method: "POST",
                        body: formData,
                    })
                        .then(() => location.reload())
                        .catch((error) => console.error("Ошибка обновления описания:", error));
                });
            });
        });
    </script>
</head>
<body>
<?php if (!$author_id): ?>
    <p>Авторизуйтесь, чтобы смотреть раздел.</p>
<?php else: ?>
    <div class="profile-container">
        <div class="profile-header">
            <img src="<?php echo htmlspecialchars($photo); ?>" alt="Фото профиля" class="user-profile-photo">
            <div class="profile-details">
                <h1><?php echo htmlspecialchars($username); ?></h1>
                <p class="profile-description"><?php echo htmlspecialchars($description); ?></p>
            </div>
        </div>

        <div class="profile-actions">
            <a href="post.php" class="button">Создать новость</a>
            <?php if ($role === 'admin'): ?>
                <a href="admin/dashboard.php" class="button">Модерация</a>
            <?php endif; ?>
        </div>

        <input type="file" id="photo" style="display: none;">

        <div class="user-posts">
            <h2>Ваши посты</h2>
            <?php foreach ($posts as $post): ?>
                <div class="post-item">
                    <?php if (!empty($post['image'])): ?>
                        <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Изображение записи">
                    <?php endif; ?>

                    <div>
                        <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                        <p>
                            <?php echo nl2br(htmlspecialchars(mb_substr(strip_tags($post['content']), 0, 200))); ?>
                            <?php if (mb_strlen(strip_tags($post['content'])) > 200): ?>
                                ... <a href="view_post.php?id=<?php echo $post['id']; ?>">Читать далее</a>
                            <?php endif; ?>
                        </p>
                        <small>Опубликовано: <?php echo htmlspecialchars($post['created_at']); ?></small>
                        <div class="post-actions">
                            <a href="edit.php?id=<?php echo $post['id']; ?>" class="button edit-btn">Редактировать</a>

                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
<?php include 'footer.php'; ?>
</body>
</html>
