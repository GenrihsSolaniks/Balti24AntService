<?php
$servername = "localhost";
$username = "root"; // Замени на свои данные БД
$password = ""; // Если есть пароль - укажи
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

// Логируем полученные данные в error_log
error_log("Получены данные: ID=$event_id, Заказ=$order_id, Сотр=$employee_id, Время=$start_time-$end_time");

if ($event_id && $order_id && $employee_id && $start_time && $end_time) {
    $sql = "UPDATE employee_schedule SET order_id = ?, employee_id = ?, start_time = ?, end_time = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissi", $order_id, $employee_id, $start_time, $end_time, $event_id);
    
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
