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

// Параметры пагинации
$items_per_page = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $items_per_page;

// Получение записей
$stmt = $pdo->prepare("SELECT news.id, news.title, news.content, news.image, news.created_at, users.username FROM news 
                        LEFT JOIN users ON news.author_id = users.id 
                        ORDER BY news.created_at DESC 
                        LIMIT :offset, :items_per_page");
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':items_per_page', $items_per_page, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получение общего количества записей для пагинации
$total_stmt = $pdo->query("SELECT COUNT(*) FROM news");
$total_items = $total_stmt->fetchColumn();
$total_pages = ceil($total_items / $items_per_page);
?>
<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Блог</title>
    <link rel="stylesheet" href="css/styles.css">

</head>
<body class="blog-page">
<div class="blog-container">
    <h1>Блог</h1>
    <?php if (empty($posts)): ?>
        <p>Записей пока нет.</p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="post-card">
                <img src="<?php echo htmlspecialchars($post['image'] ?? 'uploads/default.png'); ?>" alt="Изображение поста" class="post-image">
                <h2 class="post-title">
                    <a href="view_post.php?id=<?php echo $post['id']; ?>" class="blog-title-link">
                        <?php echo htmlspecialchars($post['title']); ?>
                    </a>
                </h2>

                <p class="post-excerpt"><?php echo mb_substr(strip_tags($post['content']), 0, 100); ?>...</p>
                <div class="post-reactions">

                    <a href="view_post.php?id=<?php echo $post['id']; ?>" class="reaction-button">Читать далее</a>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>" class="pagination-prev">&laquo; Назад</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="pagination-page <?php echo $i === $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>" class="pagination-next">Вперёд &raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
