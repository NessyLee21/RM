<?php
// Подключение к базе данных
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['email'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    if (!empty($name) && !empty($email)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO subscriptions (name, email, created_at) VALUES (:name, :email, NOW())
            ON DUPLICATE KEY UPDATE name = VALUES(name), created_at = NOW()");
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            echo '<p class="success-message">Вы успешно подписались!</p>';
        } catch (PDOException $e) {
            echo '<p class="error-message">Ошибка: ' . $e->getMessage() . '</p>';
        }
    } else {
        echo '<p class="error-message">Пожалуйста, заполните все поля.</p>';
    }
} else {
    echo '<p class="error-message">Некорректный запрос.</p>';
}
