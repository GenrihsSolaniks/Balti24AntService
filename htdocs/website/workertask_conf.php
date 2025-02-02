<?php
if (!isset($_COOKIE['worker_id'])) {
    die("Ошибка: worker_id не задан");
}

$workerId = (int)$_COOKIE['worker_id'];

$mysql = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysql->connect_error) {
    die("Ошибка подключения: " . $mysql->connect_error);
}

// Выбираем задачи, назначенные данному работнику, со статусом меньше 7
$query = "SELECT * FROM tasks WHERE worker_id = ? AND status < 7";
$stmt = $mysql->prepare($query);
$stmt->bind_param("i", $workerId);
$stmt->execute();
$result = $stmt->get_result();

echo "<table>";
echo "<tr><th>ID</th><th>Area</th><th>Address</th><th>Task</th><th>Status</th><th>Action</th></tr>";

$statusNames = [
    1 => 'Ожидание принятия',
    2 => 'Выехал',
    3 => 'Приехал к клиенту',
    4 => 'Приступил к работе',
    5 => 'Завершил работу',
    6 => 'Выехал обратно',
    7 => 'Приехал на базу'
];

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['area']}</td>";
    echo "<td>{$row['address']}</td>";
    echo "<td>{$row['task']}</td>";
    echo "<td>{$statusNames[$row['status']]}</td>";

    // Если статус ещё не завершён, отображаем кнопку действия
    if ($row['status'] < 7) {
        $nextStatus = $row['status'] + 1;
        echo "<td><button onclick=\"updateOrderStatus({$row['id']}, {$nextStatus})\">{$statusNames[$nextStatus]}</button></td>";
    } else {
        echo "<td>✔ Завершён</td>";
    }

    echo "</tr>";
}
echo "</table>";

$mysql->close();
?>
