<?php
// Подключение к базе данных
$servername = "localhost";  // Если используешь XAMPP, остается "localhost"
$username = "root";         // Стандартный пользователь
$password = "";             // Пароль в XAMPP пустой
$database = "balti24db";    // Название твоей базы (из phpMyAdmin)

$conn = new mysqli($servername, $username, $password, $database);

// Проверка подключения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Получаем данные из POST-запроса
$employee_id = $_POST['employee_id'];
$order_id    = $_POST['order_id'];
$date        = $_POST['date'];
$start_time  = $_POST['start_time'];
$end_time    = $_POST['end_time'];


// Проверяем, чтобы данные не были пустыми
if (empty($employee_id) || empty($order_id) || empty($date) || empty($start_time) || empty($end_time)) {
    die("Ошибка: Все поля должны быть заполнены.");
}

// Проверка пересечений времени (чтобы сотрудник не был назначен на два заказа в одно время)
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
if($result->num_rows > 0){
    die("Ошибка: сотрудник уже занят в это время.");
}

// Запрос на добавление нового события
$sql_insert = "INSERT INTO employee_schedule (employee_id, order_id, date, start_time, end_time) 
               VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql_insert);
$stmt->bind_param("iisss", $employee_id, $order_id, $date, $start_time, $end_time);

if ($stmt->execute()) {
    echo "Событие успешно сохранено!";
} else {
    echo "Ошибка при сохранении: " . $stmt->error;
}

// Закрываем соединение
$conn->close();
?>
