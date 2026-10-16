<?php
// Настройки для подключения к базе данных
$host = 'localhost'; // Хост сервера
$dbname = 'blog_db'; // Имя базы данных
$username = 'root'; // Имя пользователя базы данных
$password = 'root'; // Пароль пользователя

try {
    // Создание экземпляра PDO для подключения к базе данных
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4", // Строка подключения
        $username, // Имя пользователя
        $password // Пароль
    );

    // Установка режима обработки ошибок PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Режим извлечения данных по умолчанию
} catch (PDOException $e) {
    // Вывод ошибки и завершение скрипта
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}
?>
