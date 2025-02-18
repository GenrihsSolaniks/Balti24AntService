<?php
// Включаем вывод ошибок для отладки
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$database = "balti24db";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die(json_encode(["error" => "Ошибка подключения: " . $conn->connect_error]));
}

$sql = "SELECT id, employee_id, order_id, date, start_time, end_time, user_id FROM employee_schedule";
$result = $conn->query($sql);

$events = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $start_time = substr($row['start_time'], 0, 5);
        $end_time = substr($row['end_time'], 0, 5);
        $events[] = [
            'id' => $row['id'],
            'title' => "Заказ " . $row['order_id'] . " (Сотр. " . $row['employee_id'] . ") $start_time - $end_time",
            'start' => $row['date'] . 'T' . $row['start_time'],
            'end' => $row['date'] . 'T' . $row['end_time'],
            'user_id' => $row['user_id']
        ];
    }
} else {
    die(json_encode(["error" => "Ошибка в SQL-запросе: " . $conn->error]));
}

$conn->close();

echo json_encode($events, JSON_PRETTY_PRINT);
?>
