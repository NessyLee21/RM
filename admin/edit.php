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

// Получение ID поста
$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($post_id <= 0) {
    die("Неверный ID поста.");
}

// Получение данных поста
$stmt = $pdo->prepare("SELECT * FROM news WHERE id = :id");
$stmt->bindParam(':id', $post_id, PDO::PARAM_INT);
$stmt->execute();
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$post) {
    die("Пост не найден.");
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        // Удаление поста
        $stmt = $pdo->prepare("DELETE FROM news WHERE id = :id");
        $stmt->bindParam(':id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
        header("Location: index.php");
        exit;
    } else {
        // Обновление поста
        $title = $_POST['title'] ?? $post['title'];
        $content = $_POST['content'] ?? $post['content'];
        $imagePath = $post['image'];

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/';
            $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
            $imagePath = $uploadDir . $fileName;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                die("Ошибка загрузки изображения.");
            }
        }

        $stmt = $pdo->prepare("UPDATE news SET title = :title, content = :content, image = :image WHERE id = :id");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':image', $imagePath);
        $stmt->bindParam(':id', $post_id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: edit.php?id=$post_id");
        exit;
    }
}
?>
<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование поста</title>
    <script src="https://cdn.ckeditor.com/4.20.1/standard/ckeditor.js"></script>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<div class="edit-container">
    <h1>Редактирование поста</h1>
    <form method="POST" enctype="multipart/form-data">
        <label for="title">Заголовок:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>

        <label for="content">Контент:</label>
        <textarea id="content" name="content"><?php echo htmlspecialchars($post['content']); ?></textarea>
        <script>
            CKEDITOR.replace('content');
        </script>

        <label for="image">Изображение:</label>
        <input type="file" id="image" name="image" accept="image/*">
        <?php if (!empty($post['image'])): ?>
            <div class="current-image">
                <p>Текущее изображение:</p>
                <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Текущее изображение" style="max-width: 200px;">
            </div>
        <?php endif; ?>

        <button type="submit">Сохранить изменения</button>
        <button type="submit" name="delete" onclick="return confirm('Вы уверены, что хотите удалить этот пост?');">Удалить запись</button>
    </form>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
