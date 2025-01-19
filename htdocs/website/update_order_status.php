<?php
header('Content-Type: application/json');

// Подключение к базе данных
$mysql = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysql->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$orderId = $data['id'] ?? null;
$action = $data['action'] ?? null;

if (!$orderId || !$action) {
    echo json_encode(['success' => false, 'message' => 'Неверные данные']);
    exit();
}

if ($action === 'publishOrder') {
    $query = "UPDATE tasks SET status = 1 WHERE id = $orderId";
} elseif ($action === 'acceptOrder') {
    $workerId = $_COOKIE['worker_id'] ?? null;
    if (!$workerId) {
        echo json_encode(['success' => false, 'message' => 'Не найден worker_id']);
        exit();
    }
    $query = "UPDATE tasks SET status = 2, worker_id = $workerId WHERE id = $orderId";
} else {
    echo json_encode(['success' => false, 'message' => 'Неизвестное действие']);
    exit();
}

if ($mysql->query($query)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка запроса: ' . $mysql->error]);
}

$mysql->close();
?>

