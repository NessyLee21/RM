<?php
session_start();
require '../includes/db.php';

// Проверка авторизации
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Обновление статуса сообщения
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    $id = (int)$_POST['id'];
    $status = $_POST['status'] === '1' ? 1 : 0;
    $stmt = $pdo->prepare("UPDATE contacts SET processed = :status WHERE id = :id");
    $stmt->execute([':status' => $status, ':id' => $id]);
    header('Location: fos.php');
    exit;
}

// Параметры пагинации
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Подсчет общего количества записей
$totalStmt = $pdo->query("SELECT COUNT(*) FROM contacts");
$totalContacts = $totalStmt->fetchColumn();
$totalPages = ceil($totalContacts / $limit);

// Получение данных из таблицы contacts с учетом пагинации
$stmt = $pdo->prepare("SELECT * FROM contacts ORDER BY submitted_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ФОС</title>
    <link rel="stylesheet" href="../css/styles.css">

</head>
<body class="fos-body">
<?php include 'header.php'; ?>

<main>
    <h1 class="fos-title">Форма Обратной Связи</h1>

    <a href="dashboard.php" class="fos-button-back">Назад</a>

    <table class="fos-table">
        <thead>
        <tr>
            <th>Имя</th>
            <th>Email</th>
            <th>Сообщение</th>
            <th>Дата</th>
            <th>Статус</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($contacts)): ?>
            <?php foreach ($contacts as $contact): ?>
                <tr>
                    <td><?= htmlspecialchars($contact['name']) ?></td>
                    <td><?= htmlspecialchars($contact['email']) ?></td>
                    <td><?= htmlspecialchars($contact['message']) ?></td>
                    <td><?= htmlspecialchars($contact['submitted_at']) ?></td>
                    <td><?= $contact['processed'] ? 'Обработано' : 'Не обработано' ?></td>
                    <td>
                        <form method="post" action="fos.php">
                            <input type="hidden" name="id" value="<?= $contact['id'] ?>">
                            <input type="hidden" name="status" value="<?= $contact['processed'] ? 0 : 1 ?>">
                            <button type="submit" class="fos-toggle-button">
                                <?= $contact['processed'] ? 'Снять отметку' : 'Отметить обработанным' ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">Нет данных.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="fos-pagination">
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
