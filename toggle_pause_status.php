<?php
header('Content-Type: application/json');

$mysql = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysql->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$orderId = $data['id'] ?? null;

if (!$orderId) {
    echo json_encode(['success' => false, 'message' => 'Неверные данные']);
    exit();
}

// Проверяем текущий статус паузы
$query = "SELECT pause_status FROM tasks WHERE id = ?";
$stmt = $mysql->prepare($query);
$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Заказ не найден']);
    exit();
}

// Меняем статус паузы
$newPauseStatus = ($order['pause_status'] == 1) ? 0 : 1;
$updateQuery = "UPDATE tasks SET pause_status = ?, pause_time = NOW() WHERE id = ?";
$stmt = $mysql->prepare($updateQuery);
$stmt->bind_param("ii", $newPauseStatus, $orderId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка обновления статуса']);
}

$mysql->close();
?>
