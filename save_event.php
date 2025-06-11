<?php
// Включаем вывод ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Подключение к базе
$servername = "localhost";
$username = "root";
$password = "";
$database = "balti24db";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Получаем данные из POST-запроса
$employee_id = $_POST['employee_id'];
$order_id = $_POST['order_id'];
$user_id = $_POST['user_id'];
$date = $_POST['date'];
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];
$type = $_POST['type']; // Добавляем тип работы

// Проверка на пустые поля
if (empty($employee_id) || empty($order_id) || empty($user_id) || empty($date) || empty($start_time) || empty($end_time) || empty($type)) {
    die("Ошибка: Все поля должны быть заполнены.");
}

// ✅ **Проверка пересечений времени для этого сотрудника**
$sql_check = "SELECT * FROM employee_schedule 
              WHERE employee_id = ? 
              AND date = ? 
              AND (
                    (start_time < ? AND end_time > ?) OR
                    (start_time < ? AND end_time > ?) OR
                    (start_time >= ? AND end_time <= ?)
                  )";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("isssssss", $employee_id, $date, $end_time, $end_time, $start_time, $start_time, $start_time, $end_time);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    die("Ошибка: сотрудник уже занят в это время.");
}

// ✅ **Сохраняем заказ**
$sql_insert = "INSERT INTO employee_schedule (employee_id, order_id, date, start_time, end_time, user_id, type) 
               VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql_insert);
$stmt->bind_param("iisssis", $employee_id, $order_id, $date, $start_time, $end_time, $user_id, $type);

if ($stmt->execute()) {
    echo "The event was successfully saved!";
} else {
    echo "Error while saving: " . $stmt->error;
}

// Закрываем соединение
$conn->close();
?>
