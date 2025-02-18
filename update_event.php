<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "balti24db";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Получаем данные
$event_id = $_POST['id'] ?? null;
$order_id = $_POST['order_id'] ?? null;
$employee_id = $_POST['employee_id'] ?? null;
$start_time = $_POST['start_time'] ?? null;
$end_time = $_POST['end_time'] ?? null;
$user_id = $_POST['user_id'] ?? null;

if ($event_id && $order_id && $employee_id && $start_time && $end_time && $user_id) {
    $sql = "UPDATE employee_schedule SET order_id = ?, employee_id = ?, start_time = ?, end_time = ?, user_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissii", $order_id, $employee_id, $start_time, $end_time, $user_id, $event_id);
    
    if ($stmt->execute()) {
        echo "Событие обновлено!";
    } else {
        echo "Ошибка при обновлении: " . $conn->error;
    }
} else {
    echo "Ошибка: Некорректные данные!";
}

$conn->close();
?>
