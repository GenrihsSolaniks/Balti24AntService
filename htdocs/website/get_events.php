<?php
// Указываем, что будем возвращать JSON
header('Content-Type: application/json');

// Подключение к базе данных
$servername = "localhost";  // Если у тебя XAMPP, остается "localhost"
$username = "root";         // Стандартный пользователь XAMPP
$password = "";             // Пароль по умолчанию пустой
$database = "balti24db";    // Твоя база данных (по названию из phpMyAdmin)

$conn = new mysqli($servername, $username, $password, $database);

// Проверяем подключение
if ($conn->connect_error) {
    die(json_encode(["error" => "Ошибка подключения: " . $conn->connect_error]));
}

// Запрос к базе данных
$sql = "SELECT id, employee_id, order_id, date, start_time, end_time FROM employee_schedule";
$result = $conn->query($sql);


// Проверяем, есть ли данные
$events = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $start_time = substr($row['start_time'], 0, 5); // Берём только HH:MM
        $end_time = substr($row['end_time'], 0, 5);
        $events[] = [
            'id'    => $row['id'],
            'title' => "Заказ " . $row['order_id'] . " (Сотрудник " . $row['employee_id'] . ") " . $start_time . "-" . $end_time,
            'start' => $row['date'] . 'T' . $row['start_time'],
            'end'   => $row['date'] . 'T' . $row['end_time']
        ];     
    }
}

// Закрываем соединение
$conn->close();


// Выводим JSON
echo json_encode($events);
?>
