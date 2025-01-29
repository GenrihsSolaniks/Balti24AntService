<?php
header('Content-Type: application/json');

$mysql = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysql->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных']);
    exit();
}

// Получаем данные из запроса
$data = json_decode(file_get_contents('php://input'), true);
$orderId = $data['id'] ?? null;
$action = $data['action'] ?? null;

if (!$orderId || !$action) {
    echo json_encode(['success' => false, 'message' => 'Неверные данные']);
    exit();
}

// Проверяем текущий статус заказа
$query = "SELECT * FROM tasks WHERE id = ?";
$stmt = $mysql->prepare($query);
$stmt->bind_param("i", $orderId);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Заказ не найден']);
    exit();
}

// Если действие "acceptOrder", обновляем статус
if ($action === 'acceptOrder') {
    $updateQuery = "UPDATE tasks SET status = 2 WHERE id = ?";
    $stmt = $mysql->prepare($updateQuery);
    $stmt->bind_param("i", $orderId);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка обновления']);
    }
}

// Если заказ выполнен, переносим его в completetask
if ($action === 'completeOrder') {
    // Переносим данные в таблицу completetask
    $insertQuery = "INSERT INTO completetask (id, user_id, area, address, city, country, date, task, additional, worker_id, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 2)";
    $stmt = $mysql->prepare($insertQuery);
    $stmt->bind_param("iisssssssi", $order['id'], $order['user_id'], $order['area'], $order['address'], 
                                      $order['city'], $order['country'], $order['date'], $order['task'], 
                                      $order['additional'], $order['worker_id']);
    if ($stmt->execute()) {
        // Удаляем из tasks
        $deleteQuery = "DELETE FROM tasks WHERE id = ?";
        $stmt = $mysql->prepare($deleteQuery);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ошибка переноса заказа']);
    }
}

$mysql->close();
?>
