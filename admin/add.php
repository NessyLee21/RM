<?php
require '../includes/db.php'; // Подключение к базе данных с использованием $pdo

$message = ''; // Для хранения сообщения о статусе

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение данных из формы
    $title = $_POST['title'];
    $content = $_POST['content'];
    $imagePath = null;

    // Проверяем, загружено ли изображение
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../uploads/"; // Директория для загрузки
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true); // Создаём папку, если её нет
        }

        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $imagePath = $targetDir . $imageName;
        $imageType = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));

        // Проверка допустимых типов файлов
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageType, $allowedTypes)) {
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                $message = "Ошибка загрузки изображения.";
            }
        } else {
            $message = "Недопустимый формат изображения. Разрешены: jpg, jpeg, png, gif.";
        }

        // Удаляем "../" для корректного хранения в БД
        $imagePath = str_replace("../", "", $imagePath);
    }

    // Если нет ошибок загрузки изображения
    if (empty($message)) {
        // Подготовка SQL-запроса
        try {
            $stmt = $pdo->prepare("
                INSERT INTO news (title, image, content, created_at) 
                VALUES (?, ?, ?, NOW())
            ");
            if ($stmt->execute([$title, $imagePath, $content])) {
                $message = "Новость успешно добавлена!";
            } else {
                $message = "Ошибка добавления новости.";
            }
        } catch (PDOException $e) {
            $message = "Ошибка базы данных: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить новость</title>
</head>
<body>
<h1>Добавить новость</h1>

<?php if (!empty($message)): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data"> <!-- Добавлен enctype -->
    <label>Заголовок:</label>
    <input type="text" name="title" required>

    <label>Контент:</label>
    <textarea name="content" required></textarea>

    <label>Изображение:</label>
    <input type="file" name="image">

    <button type="submit">Добавить</button>
</form>
</body>
</html>
