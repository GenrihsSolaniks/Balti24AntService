<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die(json_encode(["error" => "Ошибка: Необходима авторизация."]));
}

$user_id = $_SESSION['user_id']; // ID текущего пользователя

$conn = new mysqli('localhost', 'root', '', 'balti24db');
if ($conn->connect_error) {
    die(json_encode(["error" => "Ошибка подключения: " . $conn->connect_error]));
}

// Запрос заказов для конкретного пользователя
$sql = "SELECT order_id, date, start_time, end_time, employee_id FROM employee_schedule WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = [
        "order_id"   => $row["order_id"],
        "date"       => $row["date"],
        "start_time" => substr($row["start_time"], 0, 5),
        "end_time"   => substr($row["end_time"], 0, 5),
        "employee_id" => $row["employee_id"]
    ];
}

$conn->close();
echo json_encode($orders);
?>
