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

if (!$orderId || !is_numeric($action)) {
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

if ($action === 'togglePause') {
    if ($row['pause_status'] == 0) {
        // Ставим на паузу и добавляем запись в историю
        $mysql->query("UPDATE tasks SET pause_status = 1 WHERE id = $orderId");
        $mysql->query("INSERT INTO pause_history (task_id, pause_time) VALUES ($orderId, NOW())");
    } else {
        // Снимаем с паузы и обновляем запись с временем возобновления
        $mysql->query("UPDATE tasks SET pause_status = 0 WHERE id = $orderId");
        $mysql->query("UPDATE pause_history SET resume_time = NOW() WHERE task_id = $orderId AND resume_time IS NULL");
    }

    echo json_encode(['success' => true]);
    exit();
}


// Обновляем статус заказа
$updateQuery = "UPDATE tasks SET status = ? WHERE id = ?";
$stmt = $mysql->prepare($updateQuery);
$stmt->bind_param("ii", $action, $orderId);

if ($stmt->execute()) {
    // Если статус равен 7 (заказ завершён), переносим его в таблицу выполненных заказов
    if ($action == 7) {
        $insertQuery = "INSERT INTO completetask (id, user_id, area, address, city, country, date, task, additional, worker_id, status) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mysql->prepare($insertQuery);
        $stmt->bind_param("iisssssssis", $order['id'], $order['user_id'], $order['area'], $order['address'], 
                                           $order['city'], $order['country'], $order['date'], $order['task'], 
                                           $order['additional'], $order['worker_id'], $action);

        if ($stmt->execute()) {
            // Удаляем заказ из текущей таблицы
            $deleteQuery = "DELETE FROM tasks WHERE id = ?";
            $stmt = $mysql->prepare($deleteQuery);
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
        }
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка обновления статуса']);
}

$mysql->close();
?>
