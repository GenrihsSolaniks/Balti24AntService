<?php
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

// Фильтры (получаем из запроса)
$employee_id = isset($_GET['employee_id']) ? $_GET['employee_id'] : null;
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
$date = isset($_GET['date']) ? $_GET['date'] : null;
$type = isset($_GET['type']) ? $_GET['type'] : null;

// Базовый SQL-запрос
$sql = "SELECT id, employee_id, order_id, date, start_time, end_time, user_id, type FROM employee_schedule WHERE 1=1";

// Добавляем фильтры, если они переданы
if (!empty($employee_id)) {
    $sql .= " AND employee_id = " . intval($employee_id);
}
if (!empty($user_id)) {
    $sql .= " AND user_id = " . intval($user_id);
}
if (!empty($date)) {
    $sql .= " AND date = '" . $conn->real_escape_string($date) . "'";
}
if (!empty($type)) {
    $sql .= " AND type = '" . $conn->real_escape_string($type) . "'";
}

// Выполняем запрос
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
            'user_id' => $row['user_id'],
            'type' => $row['type']
        ];
    }
} else {
    die(json_encode(["error" => "Ошибка в SQL-запросе: " . $conn->error]));
}

$conn->close();
echo json_encode($events, JSON_PRETTY_PRINT);
?>
