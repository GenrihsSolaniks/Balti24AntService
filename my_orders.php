<?php
session_start();
$mysqli = new mysqli('localhost', 'root', '', 'balti24db');

// Проверка входа
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Получаем все заказы пользователя
$stmt = $mysqli->prepare("SELECT id, date, area, city, status FROM tasks WHERE user_id = ? ORDER BY date DESC");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Получаем все завершенные заказы из таблицы completetask
$completedStmt = $mysqli->prepare("SELECT id, date, area, city, status FROM completetask WHERE user_id = ? ORDER BY date DESC");
$completedStmt->bind_param('i', $user_id);
$completedStmt->execute();
$completedResult = $completedStmt->get_result();

?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Мои заказы - Balti24</title>
  <link rel="stylesheet" href="stylelegacy.css">
</head>
<body>
<header class="header">
        <div class="container">
            <div class="logo">Balti24</div>
            <nav class="nav">
                <ul>
                    <li><a href="index.html">Главная</a></li>
                    <li><a href="about.html">О нас</a></li>
                    <li><a href="my_orders.php">Заказы</a></li>
                    <li class="me-3"><a href="user_order.php">Время моих заказов</a></li>
                    <li><a href="MainSite.php">Заполнить форму заказа</a></li>
                    <li><a href="contact.html">Контакты</a></li>
                </ul>
            </nav>
        </div>
</header>
<div class="container">
    <h1>Активные заказы</h1>
    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Дата</th>
                <th>Услуга</th>
                <th>Город</th>
                <th>Статус</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['date']) ?></td>
                    <td><?= htmlspecialchars($row['area']) ?></td>
                    <td><?= htmlspecialchars($row['city']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>У вас пока нет активных заказов.</p>
    <?php endif; ?>
</div>


<div class="container">
    <h1>Завершенные заказы</h1>
    <?php if ($completedResult->num_rows > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Дата</th>
                <th>Услуга</th>
                <th>Город</th>
                <th>Статус</th>
            </tr>
            <?php while($row = $completedResult->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['date']) ?></td>
                    <td><?= htmlspecialchars($row['area']) ?></td>
                    <td><?= htmlspecialchars($row['city']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>У вас пока нет завершённых заказов.</p>
    <?php endif; ?>
</div>

<footer class="footer">
        <div class="container">
            <p>&copy; 2025 Balti24. Все права защищены.</p>
        </div>
    </footer>
</body>
</html>
