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
$type = $_POST['type'] ?? null;

if (!$event_id || !$order_id || !$employee_id || !$start_time || !$end_time || !$user_id || !$type) {
    die("Ошибка: Некорректные данные!");
}

// ✅ **Проверяем пересечения времени (исключая текущее событие)**
$sql_check = "SELECT * FROM employee_schedule 
              WHERE employee_id = ? 
              AND date = (SELECT date FROM employee_schedule WHERE id = ?) 
              AND id != ? 
              AND (
                    (start_time < ? AND end_time > ?) OR
                    (start_time < ? AND end_time > ?) OR
                    (start_time >= ? AND end_time <= ?)
                  )";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("iiiiissss", $employee_id, $event_id, $event_id, $end_time, $end_time, $start_time, $start_time, $start_time, $end_time);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    die("Ошибка: сотрудник уже занят в это время.");
}

// ✅ **Обновляем данные**
$sql = "UPDATE employee_schedule SET order_id = ?, employee_id = ?, start_time = ?, end_time = ?, user_id = ?, type = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iissisi", $order_id, $employee_id, $start_time, $end_time, $user_id, $type, $event_id);

if ($stmt->execute()) {
    echo "Событие обновлено!";
} else {
    echo "Ошибка при обновлении: " . $stmt->error;
}

$conn->close();
?>
