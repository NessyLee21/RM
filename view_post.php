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

// Получение ID записи
$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($post_id <= 0) {
    die("Неверный ID записи.");
}

// Получение данных записи
$stmt = $pdo->prepare("SELECT * FROM news WHERE id = :id");
$stmt->bindParam(':id', $post_id, PDO::PARAM_INT);
$stmt->execute();
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$post) {
    die("Запись не найдена.");
}

// Подготовка ссылки для соцсетей
$share_url = urlencode("http://rmnews.com/view_post.php?id=" . $post_id);
$share_text = urlencode($post['title']);
?>
<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="post-wrapper">
    <?php if (!empty($post['image'])): ?>
        <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Изображение записи" class="post-image">
    <?php endif; ?>

    <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>

    <div class="post-content">
        <?php echo $post['content']; ?>
    </div>

    <div class="share-and-back">
        <div class="share-buttons">
            <a href="https://t.me/share/url?url=<?php echo $share_url; ?>&text=<?php echo $share_text; ?>" target="_blank">
                <img src="uploads/telegram-icon.png" alt="Telegram">
            </a>
            <a href="https://api.whatsapp.com/send?text=<?php echo $share_text . ' ' . $share_url; ?>" target="_blank">
                <img src="uploads/whatsapp-icon.png" alt="WhatsApp">
            </a>
        </div>

        <a href="javascript:history.back()" class="back-button">Назад</a>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
