<?php
// Подключение к базе данных
$servername = "localhost";
$username = "root";
$password = "";
$database = "balti24db";

$conn = new mysqli($servername, $username, $password, $database);

// Проверка подключения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Получаем данные из POST-запроса
$employee_id = $_POST['employee_id'] ?? null;
$order_id    = $_POST['order_id'] ?? null;
$user_id     = $_POST['user_id'] ?? null;
$date        = $_POST['date'] ?? null;
$start_time  = $_POST['start_time'] ?? null;
$end_time    = $_POST['end_time'] ?? null;

// Проверяем, чтобы данные не были пустыми
if (!$employee_id || !$order_id || !$user_id || !$date || !$start_time || !$end_time) {
    die("Ошибка: Все поля должны быть заполнены.");
}

// Запрос на добавление нового события
$sql_insert = "INSERT INTO employee_schedule (employee_id, order_id, date, start_time, end_time, user_id) 
               VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql_insert);
$stmt->bind_param("iisssi", $employee_id, $order_id, $date, $start_time, $end_time, $user_id);

if ($stmt->execute()) {
    echo "Событие успешно сохранено!";
} else {
    echo "Ошибка при сохранении: " . $stmt->error;
}

// Закрываем соединение
$conn->close();
?>
