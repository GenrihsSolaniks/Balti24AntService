<?php
header('Content-Type: application/json');

// Подключение к базе данных
$mysql = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysql->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных']);
    exit();
}

// Получение ID работника
$workerId = isset($_GET['worker_id']) ? (int)$_GET['worker_id'] : null;

if (!$workerId) {
    echo json_encode(['success' => false, 'message' => 'Не указан worker_id']);
    exit();
}

// Получение расписания сотрудника
$stmt = $mysql->prepare("SELECT start_time, end_time FROM worker_schedule WHERE worker_id = ? AND status = 'busy'");
$stmt->bind_param("i", $workerId);
$stmt->execute();
$result = $stmt->get_result();

$schedule = [];
while ($row = $result->fetch_assoc()) {
    $schedule[] = [
        "title" => "Занято",
        "start" => $row['start_time'],
        "end" => $row['end_time'],
        "color" => "red"
    ];
}

echo json_encode(['success' => true, 'events' => $schedule]);

$stmt->close();
$mysql->close();
?>
