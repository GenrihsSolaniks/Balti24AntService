<?php
header('Content-Type: application/json');

// Подключение к базе данных
$mysql = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysql->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection error']);
    exit();
}

// Получение данных из запроса
$data = json_decode(file_get_contents('php://input'), true);
$workerId = isset($data['worker_id']) ? (int)$data['worker_id'] : null;
$startTime = isset($data['start_time']) ? $data['start_time'] : null;
$endTime = isset($data['end_time']) ? $data['end_time'] : null;

if (!$workerId || !$startTime || !$endTime) {
    echo json_encode(['success' => false, 'message' => 'Incorrect data']);
    exit();
}

// Добавление записи в расписание
$stmt = $mysql->prepare("INSERT INTO worker_schedule (worker_id, start_time, end_time, status) VALUES (?, ?, ?, 'busy')");
$stmt->bind_param("iss", $workerId, $startTime, $endTime);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Employment added']);
} else {
    echo json_encode(['success' => false, 'message' => 'Request error: ' . $stmt->error]);
}

$stmt->close();
$mysql->close();
?>
