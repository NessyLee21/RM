<?php
session_start();
require '../includes/db.php';

// Проверка авторизации
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Параметры пагинации
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Подсчет общего количества записей
$totalStmt = $pdo->query("SELECT COUNT(*) FROM news");
$totalNews = $totalStmt->fetchColumn();
$totalPages = ceil($totalNews / $limit);

// Получение новостей с учетом пагинации
$stmt = $pdo->prepare("SELECT * FROM news ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$news = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ Панель</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            text-align: center;
        }

        footer {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            text-align: center;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        h1 {
            margin: 20px 0;
            text-align: center;
            color: #444;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        a {
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        .button {
            display: inline-block;
            padding: 10px 15px;
            margin: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            text-align: center;
        }

        .button:hover {
            background-color: #0056b3;
        }

        img {
            display: block;
            margin: 0 auto;
            max-width: 150px;
            height: auto;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        p {
            text-align: center;
            margin: 10px 0;
        }

        .news-content {
            display: none;
            margin: 20px auto;
            width: 90%;
            padding: 15px;
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .news-content p {
            margin: 0;
        }

        .pagination {
            text-align: center;
            margin: 20px 0;
        }

        .pagination a {
            display: inline-block;
            margin: 0 5px;
            padding: 5px 10px;
            background-color: #007BFF;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
        }

        .pagination a:hover {
            background-color: #0056b3;
        }

        .pagination .current {
            background-color: #0056b3;
            pointer-events: none;
        }

        .button-container {
            text-align: center;
            margin: 20px 0 70px; /* добавлено нижнее расстояние */
        }
    </style>
    <script>
        function toggleContent(id) {
            const content = document.getElementById(`content-${id}`);
            if (content.style.display === 'none' || content.style.display === '') {
                content.style.display = 'block';
            } else {
                content.style.display = 'none';
            }
        }
    </script>
</head>
<body>
<?php include 'header.php'; ?>

<main>
    <h1>Админ Панель</h1>
    <p>Добро пожаловать, <?= htmlspecialchars($_SESSION['username']) ?>!</p>

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Фото</th>
            <th>Заголовок</th>
            <th>Дата</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($news)): ?>
            <?php foreach ($news as $row): ?>
                <tr onclick="toggleContent(<?= $row['id'] ?>)">
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td>
                        <?php if (!empty($row['image'])): ?>
                            <img src="../assets/<?= htmlspecialchars($row['image']) ?>" alt="Фото">
                        <?php else: ?>
                            Нет изображения
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                    <td>
                        <a href="edit.php?id=<?= $row['id'] ?>">Редактировать</a> |
                        <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Удалить новость?');">Удалить</a>
                    </td>
                </tr>
                <tr>
                    <td colspan="5">
                        <div id="content-<?= $row['id'] ?>" class="news-content">
                            <p><?= htmlspecialchars($row['content']) ?></p>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Нет новостей.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>">&laquo; Назад</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= $i === $page ? 'current' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>">Вперед &raquo;</a>
        <?php endif; ?>
    </div>

    <div class="button-container">
        <a href="subscriptions.php" class="button">Подписки</a>
        <a href="fos.php" class="button">ФОС</a>
    </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
