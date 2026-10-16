<?php
session_start();
require '../includes/db.php';

// Проверка авторизации
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Обновление статуса подписки
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    $id = (int)$_POST['id'];
    $status = $_POST['status'] === '1' ? 1 : 0;
    $stmt = $pdo->prepare("UPDATE subscriptions SET processed = :status WHERE id = :id");
    $stmt->execute([':status' => $status, ':id' => $id]);
    header('Location: subscriptions.php');
    exit;
}

// Параметры пагинации
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Подсчет общего количества записей
$totalStmt = $pdo->query("SELECT COUNT(*) FROM subscriptions");
$totalSubscriptions = $totalStmt->fetchColumn();
$totalPages = ceil($totalSubscriptions / $limit);

// Получение данных из таблицы subscriptions с учетом пагинации
$stmt = $pdo->prepare("SELECT * FROM subscriptions ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Подписки</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        body.subscriptions-body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }

        header.subscriptions-header {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            text-align: center;
        }

        footer.subscriptions-footer {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            text-align: center;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        h1.subscriptions-title {
            margin: 20px 0;
            text-align: center;
            color: #444;
        }

        table.subscriptions-table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        table.subscriptions-table th, table.subscriptions-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        table.subscriptions-table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }

        table.subscriptions-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table.subscriptions-table tr:hover {
            background-color: #f1f1f1;
        }

        .subscriptions-pagination {
            text-align: center;
            margin: 20px 0;
        }

        .subscriptions-pagination a {
            display: inline-block;
            margin: 0 5px;
            padding: 5px 10px;
            background-color: #007BFF;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
        }

        .subscriptions-pagination a:hover {
            background-color: #0056b3;
        }

        .subscriptions-pagination .current {
            background-color: #0056b3;
            pointer-events: none;
        }

        .subscriptions-toggle-button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .subscriptions-toggle-button:hover {
            background-color: #0056b3;
        }

        .subscriptions-button-back {
            display: block;
            width: 150px;
            margin: 20px auto;
            text-align: center;
            background-color: #007BFF;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
        }

        .subscriptions-button-back:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body class="subscriptions-body">
<?php include 'header.php'; ?>

<main>
    <h1 class="subscriptions-title">Подписки</h1>

    <a href="dashboard.php" class="subscriptions-button-back">Назад</a>

    <table class="subscriptions-table">
        <thead>
        <tr>
            <th>Имя</th>
            <th>Email</th>
            <th>Дата</th>
            <th>Статус</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($subscriptions)): ?>
            <?php foreach ($subscriptions as $subscription): ?>
                <tr>
                    <td><?= htmlspecialchars($subscription['name']) ?></td>
                    <td><?= htmlspecialchars($subscription['email']) ?></td>
                    <td><?= htmlspecialchars($subscription['created_at']) ?></td>
                    <td><?= $subscription['processed'] ? 'Обработано' : 'Не обработано' ?></td>
                    <td>
                        <form method="post" action="subscriptions.php">
                            <input type="hidden" name="id" value="<?= $subscription['id'] ?>">
                            <input type="hidden" name="status" value="<?= $subscription['processed'] ? 0 : 1 ?>">
                            <button type="submit" class="subscriptions-toggle-button">
                                <?= $subscription['processed'] ? 'Снять отметку' : 'Отметить обработанным' ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Нет данных.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="subscriptions-pagination">
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
</main>

<?php include 'footer.php'; ?>
</body>
</html>
