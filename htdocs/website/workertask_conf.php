<?php
if (!isset($_COOKIE['worker_id'])) {
    die("Ошибка: worker_id не задан");
}

$workerId = (int)$_COOKIE['worker_id'];

$mysql = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysql->connect_error) {
    die("Ошибка подключения: " . $mysql->connect_error);
}

// Выбираем задачи, назначенные данному работнику, со статусом 1
$query = "SELECT * FROM tasks WHERE worker_id = ? AND status = 1";
$stmt = $mysql->prepare($query);
$stmt->bind_param("i", $workerId);
$stmt->execute();
$result = $stmt->get_result();

echo "<table>";
echo "<tr><th>ID</th><th>User ID</th><th>Area</th><th>Address</th><th>City</th><th>Country</th><th>Date</th><th>Task</th><th>Additional</th><th>Action</th></tr>";

while ($row = $result->fetch_assoc()) {
    $rowClass = ($row['status'] == 2) ? 'class="highlight-green"' : '';

    echo "<tr $rowClass>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['user_id']}</td>";
    echo "<td>{$row['area']}</td>";
    echo "<td>{$row['address']}</td>";
    echo "<td>{$row['city']}</td>";
    echo "<td>{$row['country']}</td>";
    echo "<td>{$row['date']}</td>";
    echo "<td>{$row['task']}</td>";
    echo "<td>{$row['additional']}</td>";

    if ($row['status'] == 1) {
        echo "<td><button onclick=\"acceptOrder({$row['id']})\">Принять заказ</button></td>";
    } elseif ($row['status'] == 2) {
        echo "<td><button onclick=\"completeOrder({$row['id']})\">Завершить</button></td>";
    }

    echo "</tr>";
}
echo "</table>";

$mysql->close();
?>
