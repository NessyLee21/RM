<?php
// Параметры подключения к базе данных
$host = 'localhost';
$db = 'blog_db';
$user = 'root';
$password = 'root';

try {
    // Создание подключения через PDO
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Обработка отправленной формы
    $formSubmissionMessage = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $message = htmlspecialchars($_POST['message']);

        // Вставка данных в таблицу contacts
        $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message, submitted_at) VALUES (:name, :email, :message, NOW())");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':message', $message);

        if ($stmt->execute()) {
            $formSubmissionMessage = "<p>Ваше сообщение успешно отправлено!</p>";
        } else {
            $formSubmissionMessage = "<p>Произошла ошибка при отправке сообщения. Попробуйте позже.</p>";
        }
    }
} catch (PDOException $e) {
    die("<p>Ошибка подключения к базе данных: " . $e->getMessage() . "</p>");
}
?>

<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Контакты - RM News</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="contact-page-body">
<div class="contact-container">
    <div class="contact-header">
        <h1>Свяжитесь с нами</h1>
    </div>
    <div class="contact-description">
        Мы всегда готовы помочь вам! Свяжитесь с нами по любому вопросу через форму обратной связи или посетите наш офис.
    </div>

    <div class="contact-section">
        <div class="contact-info">
            <h3>Наши контакты</h3>
            <p><strong>Телефон:</strong> +7 (495) 123-45-67</p>
            <p><strong>Email:</strong> support@rmnews.com</p>
            <p><strong>Адрес:</strong> Москва, проспект Технологий, 15</p>
            <p><strong>Часы работы:</strong> Пн-Пт: 9:00 - 18:00</p>
            <img src="uploads/unnamed.jpg" alt="Описание изображения">
        </div>
        <div class="contact-form">
            <h3>Форма обратной связи</h3>
            <form action="" method="post">
                <label for="name">Ваше имя:</label>
                <input type="text" id="name" name="name" placeholder="Введите ваше имя" required>

                <label for="email">Ваш Email:</label>
                <input type="email" id="email" name="email" placeholder="Введите ваш email" required>

                <label for="message">Сообщение:</label>
                <textarea id="message" name="message" placeholder="Введите ваше сообщение" required></textarea>

                <button type="submit">Отправить</button>
            </form>
            <?php echo $formSubmissionMessage; ?>
        </div>
    </div>

    <div class="contact-map">
        <h3>Где мы находимся</h3>
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d71878.41566088222!2d37.35316949726563!3d55.73764540000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46b54bfde5921bed%3A0xade62363c9430996!2z0JTQtdC70L7QstC-0Lkg0KbQtdC90YLRgCAi0J_QsNGA0Log0L_QvtCx0LXQtNGLIg!5e0!3m2!1sru!2sru!4v1736690743923!5m2!1sru!2sru" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
