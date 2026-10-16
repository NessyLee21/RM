<?php
session_start();
require '../includes/db.php'; // Подключение к базе данных

// Проверка авторизации
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Удаление записи
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Удаление изображения, если оно есть
    $stmt = $pdo->prepare("SELECT image FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $news = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($news && !empty($news['image'])) {
        $imagePath = "../" . $news['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath); // Удаляем файл
        }
    }

    // Удаление новости из базы
    $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
    if ($stmt->execute([$id])) {
        header("Location: dashboard.php?message=deleted");
        exit;
    } else {
        echo "Ошибка удаления новости.";
    }
} else {
    header("Location: dashboard.php");
    exit;
}
?>
