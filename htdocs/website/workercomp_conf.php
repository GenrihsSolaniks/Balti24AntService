<?php
if (!isset($_COOKIE['worker_id'])) {
    header('Location: workeraunth.html');
    exit();
}

$workerId = (int)$_COOKIE['worker_id'];

$mysql = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysql->connect_error) {
    die("Ошибка подключения: " . $mysql->connect_error);
}

// Выбираем выполненные задания данного работника
$query = "SELECT * FROM completetask WHERE worker_id = ?";
$stmt = $mysql->prepare($query);
$stmt->bind_param("i", $workerId);
$stmt->execute();
$result = $stmt->get_result();

echo "<table>";
echo "<tr><th>ID</th><th>User ID</th><th>Area</th><th>Address</th><th>City</th><th>Country</th><th>Date</th><th>Task</th><th>Additional</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['user_id']}</td>";
    echo "<td>{$row['area']}</td>";
    echo "<td>{$row['address']}</td>";
    echo "<td>{$row['city']}</td>";
    echo "<td>{$row['country']}</td>";
    echo "<td>{$row['date']}</td>";
    echo "<td>{$row['task']}</td>";
    echo "<td>{$row['additional']}</td>";
    echo "</tr>";
}
echo "</table>";

$mysql->close();
?>
