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
  <title>My orders - Balti24</title>
  <link rel="stylesheet" href="stylelegacy.css">
</head>
<body>
<header class="header">
        <div class="container">
            <div class="logo">Balti24</div>
            <nav class="nav">
                <ul>
                    <li><a href="index.html">Home</a></li>
                    <li><a href="about.html">About</a></li>
                    <li><a href="my_orders.php">Orders</a></li>
                    <li class="me-3"><a href="user_order.php">The time of my orders</a></li>
                    <li><a href="MainSite.php">Fill out the order form</a></li>
                    <li><a href="contact.html">Contacts</a></li>
                </ul>
            </nav>
        </div>
</header>
<div class="container">
    <h1>Active orders</h1>
    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Service</th>
                <th>City</th>
                <th>Status</th>
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
        <p>You do not have any active orders yet.</p>
    <?php endif; ?>
</div>


<div class="container">
    <h1>Completed orders</h1>
    <?php if ($completedResult->num_rows > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Service</th>
                <th>City</th>
                <th>Status</th>
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
        <p>You don't have any completed orders yet.</p>
    <?php endif; ?>
</div>

<footer class="footer">
        <div class="container">
            <p>&copy; 2025 Balti24. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
