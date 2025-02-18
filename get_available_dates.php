<?php
header('Content-Type: application/json');

// Подключение к базе данных
$mysql = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysql->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных']);
    exit();
}

// Получение ID услуги
$serviceId = isset($_GET['service_id']) ? (int)$_GET['service_id'] : null;

if (!$serviceId) {
    echo json_encode(['success' => false, 'message' => 'Не указан service_id']);
    exit();
}

// Получение занятых дат по услуге
$stmt = $mysql->prepare("
    SELECT DISTINCT start_time FROM worker_schedule 
    WHERE worker_id IN (SELECT id FROM workers WHERE type = ?) 
    AND status = 'busy'
");
$stmt->bind_param("s", $serviceId);
$stmt->execute();
$result = $stmt->get_result();

$busyDates = [];
while ($row = $result->fetch_assoc()) {
    $busyDates[] = ["start" => $row['start_time'], "color" => "red"];
}

echo json_encode(['success' => true, 'busy_dates' => $busyDates]);

$stmt->close();
$mysql->close();
?>
