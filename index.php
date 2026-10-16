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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная страница</title>
    <link rel="stylesheet" href="css/styles.css">
    <script>
        async function submitForm(event) {
            event.preventDefault(); // Предотвращает стандартное поведение формы

            const form = document.getElementById('subscribeForm');
            const formData = new FormData(form);

            try {
                const response = await fetch('subscribe_handler.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.text();
                document.getElementById('subscribe-message').innerHTML = result;

                // Очистка формы после успешной отправки
                if (response.ok) {
                    form.reset();
                }
            } catch (error) {
                document.getElementById('subscribe-message').innerHTML = `<p class="error-message">Ошибка: ${error.message}</p>`;
            }
        }
    </script>
</head>
<body>
<?php include 'header.php'; ?>

<!-- Слайдер -->
<div class="header-slider">
    <div class="slider-wrapper">
        <div class="slide">
            <img src="uploads/slide1.jpg" alt="Слайд 1">
        </div>
        <div class="slide">
            <img src="uploads/slide2.jpg" alt="Слайд 2">
        </div>
    </div>
    <button class="slider-btn prev"></button>
    <button class="slider-btn next"></button>
</div>

<!-- Подключаем JavaScript -->
<script src="js/slider.js"></script>


<!-- Основной контент -->
<div class="news-section">
    <h2>Главные новости</h2>
    <div class="featured-news-list">
        <?php
        try {
            $stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 2");
            $featuredNews = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($featuredNews)) {
                foreach ($featuredNews as $news) {
                    echo '<div class="featured-news-item">';
                    echo '<img src="' . htmlspecialchars($news['image']) . '" alt="Изображение новости">';
                    echo '<div class="content">';
                    echo '<h3>' . htmlspecialchars($news['title']) . '</h3>';
                    echo '<p>' . htmlspecialchars(mb_substr(strip_tags($news['content']), 0, 150)) . '...</p>';
                    echo '<a href="view_post.php?id=' . $news['id'] . '">Смотреть</a>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>Нет доступных новостей.</p>';
            }
        } catch (Exception $e) {
            echo '<p>Ошибка: ' . $e->getMessage() . '</p>';
        }
        ?>
    </div>

    <h2>Другие новости</h2>
    <div class="other-news-grid">
        <?php
        try {
            $stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 6 OFFSET 2");
            $otherNews = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($otherNews)) {
                foreach ($otherNews as $news) {
                    echo '<div class="news-card">';
                    echo '<img src="' . htmlspecialchars($news['image']) . '" alt="Изображение новости">';
                    echo '<div class="content">';
                    echo '<h3>' . htmlspecialchars($news['title']) . '</h3>';
                    echo '<a href="view_post.php?id=' . $news['id'] . '">Читать далее</a>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>Нет дополнительных новостей.</p>';
            }
        } catch (Exception $e) {
            echo '<p>Ошибка: ' . $e->getMessage() . '</p>';
        }
        ?>
    </div>
</div>

<!-- Блок подписки -->
<div class="subscribe-block">
    <h2>Подписаться</h2>
    <div id="subscribe-message"></div>
    <form class="subscribe-form" id="subscribeForm" onsubmit="submitForm(event)">
        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <button type="submit">Представить на рассмотрение</button>
    </form>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
