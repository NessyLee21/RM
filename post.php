<?php
session_start(); // Убедитесь, что сессия стартует
include 'header.php';

// Подключение к базе данных
include 'includes/db.php';

// Проверьте, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    die("Вы не авторизованы. Пожалуйста, войдите в систему.");
}

$userId = $_SESSION['user_id']; // Получаем ID текущего пользователя

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение данных из формы
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $imageName = '';

    // Проверка, загрузил ли пользователь изображение
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = 'uploads/';

        // Создаём папку, если её нет
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $uploadFilePath = $uploadDir . $imageName;

        // Сохранение изображения в папку uploads
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadFilePath)) {
            die("Ошибка загрузки изображения. Убедитесь, что папка 'uploads/' доступна для записи.");
        }

        // Сохраняем полный путь для базы данных
        $imageName = $uploadFilePath;
    }

    // Вставка данных в базу
    $query = "INSERT INTO news (title, content, image, author_id, created_at) VALUES (:title, :content, :image, :author_id, NOW())";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':title' => $title,
            ':content' => $content,
            ':image' => $imageName,
            ':author_id' => $userId
        ]);
        echo "<script>alert('Новость успешно добавлена.');</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Ошибка при добавлении новости: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавление новости</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

</head>
<body>
<div class="news-container">
    <h2>Добавление новости</h2>
    <form id="news-form" enctype="multipart/form-data" method="POST">
        <div class="news-form-group">
            <label for="title">Заголовок:</label>
            <input type="text" id="title" name="title" placeholder="Введите заголовок" required>
        </div>
        <div class="news-form-group">
            <label for="content">Содержимое:</label>
            <textarea id="content" name="content" placeholder="Введите текст новости"></textarea>
        </div>
        <div class="news-form-group">
            <label for="image-upload">Добавить изображение:</label>
            <input type="file" id="image-upload" name="image" accept="image/*">
            <img id="image-preview" class="news-image-preview" alt="Предпросмотр изображения">
        </div>
        <div class="news-form-actions">
            <button type="submit" class="news-save-button">Сохранить</button>
        </div>
    </form>
</div>

<script>
    const imageUpload = document.getElementById('image-upload');
    const imagePreview = document.getElementById('image-preview');

    imageUpload.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    ClassicEditor
        .create(document.querySelector('#content'), {
            toolbar: [
                'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'blockQuote', 'insertTable', '|', 'undo', 'redo'
            ],
            height: '600px'
        })
        .catch(error => {
            console.error(error);
        });
</script>
<?php include 'footer.php'; ?>
</body>
</html>
