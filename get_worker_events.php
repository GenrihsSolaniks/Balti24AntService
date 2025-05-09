<?php
session_start();
if (!isset($_SESSION['worker_id'])) {
    die(json_encode(["error" => "Ошибка: Необходима авторизация."]));
}

$worker_id = $_SESSION['worker_id']; // ID текущего работника

$conn = new mysqli('localhost', 'root', '', 'balti24db');
if ($conn->connect_error) {
    die(json_encode(["error" => "Ошибка подключения: " . $conn->connect_error]));
}

// Запрос заказов для конкретного работника
$sql = "SELECT order_id, date, start_time, end_time FROM employee_schedule WHERE employee_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $worker_id);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = [
        "order_id"   => $row["order_id"],
        "date"       => $row["date"],
        "start_time" => substr($row["start_time"], 0, 5),
        "end_time"   => substr($row["end_time"], 0, 5)
    ];
}

$conn->close();
echo json_encode($events);
?>
