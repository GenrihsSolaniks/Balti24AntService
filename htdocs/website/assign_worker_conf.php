<?php
header('Content-Type: application/json');

// Подключение к базе данных
$mysql = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysql->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных']);
    exit();
}

// Получение данных из запроса
$data = json_decode(file_get_contents('php://input'), true);
$orderId = isset($data['order_id']) ? (int)$data['order_id'] : null;
$workerId = isset($data['worker_id']) ? (int)$data['worker_id'] : null;

if (!$orderId || !$workerId) {
    echo json_encode(['success' => false, 'message' => 'Неверные данные']);
    exit();
}

// Подготовленный запрос для защиты от SQL-инъекций
$stmt = $mysql->prepare("UPDATE tasks SET worker_id = ?, status = 1 WHERE id = ?");
$stmt->bind_param("ii", $workerId, $orderId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка запроса: ' . $stmt->error]);
}

$stmt->close();
$mysql->close();
?>

