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

if (!$orderId || empty($action)) {
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

// ========== ДОБАВЛЕННЫЙ КОД ДЛЯ ОБРАБОТКИ ПАУЗЫ ==========
if ($action === 'togglePause') {
    if ($order['pause_status'] == 0) {
        // Начинаем новую паузу
        $mysql->query("UPDATE tasks SET pause_status = 1, pause_time = NOW() WHERE id = $orderId");
        $mysql->query("INSERT INTO pause_history (task_id, pause_time) VALUES ($orderId, NOW())");
    } else {
        // Завершаем паузу и обновляем общее время
        $mysql->query("UPDATE tasks SET pause_status = 0 WHERE id = $orderId");
        $mysql->query("UPDATE pause_history SET resume_time = NOW() WHERE task_id = $orderId AND resume_time IS NULL");

        // Рассчитываем время паузы
        $pauseQuery = $mysql->prepare("
            SELECT SUM(TIMESTAMPDIFF(SECOND, pause_time, resume_time)) AS total_pause_time 
            FROM pause_history 
            WHERE task_id = ?");
        $pauseQuery->bind_param("i", $orderId);
        $pauseQuery->execute();
        $pauseRow = $pauseQuery->get_result()->fetch_assoc();
        $totalPauseTime = $pauseRow['total_pause_time'] ?? 0;

        $mysql->query("UPDATE tasks SET total_pause_time = $totalPauseTime WHERE id = $orderId");
    }
    echo json_encode(['success' => true]);
    exit();
}
// ========== КОНЕЦ ДОБАВЛЕННОГО КОДА ==========

// Обновляем статус заказа
$updateQuery = "UPDATE tasks SET status = ? WHERE id = ?";
$stmt = $mysql->prepare($updateQuery);
$stmt->bind_param("ii", $action, $orderId);

if ($stmt->execute()) {
    // Записываем изменения в task_timestamps
    if (in_array($action, [2, 4, 5, 7])) {
        $timestampQuery = "INSERT INTO task_timestamps (task_id, status, timestamp) VALUES (?, ?, NOW())";
        $stmt = $mysql->prepare($timestampQuery);
        $stmt->bind_param("ii", $orderId, $action);
        $stmt->execute();
    }

    // Если статус равен 7 (заказ завершён), переносим его в таблицу выполненных заказов
    if ($action == 7) {
        // Получаем времена из task_timestamps
        $timeQuery = $mysql->prepare("
            SELECT 
                MAX(CASE WHEN status = 2 THEN timestamp END) AS start_trip,
                MAX(CASE WHEN status = 4 THEN timestamp END) AS start_work,
                MAX(CASE WHEN status = 5 THEN timestamp END) AS end_work,
                MAX(CASE WHEN status = 7 THEN timestamp END) AS end_trip
            FROM task_timestamps
            WHERE task_id = ?
        ");
        $timeQuery->bind_param("i", $orderId);
        $timeQuery->execute();
        $timeResult = $timeQuery->get_result();
        $timeRow = $timeResult->fetch_assoc();

        $workDuration = null;
        $tripDuration = null;

        // Вычисление времени работы (4 → 5)
        if ($timeRow['start_work'] && $timeRow['end_work']) {
            $start = strtotime($timeRow['start_work']);
            $end = strtotime($timeRow['end_work']);
            $duration = $end - $start;
            $hours = floor($duration / 3600);
            $minutes = floor(($duration % 3600) / 60);
            $workDuration = sprintf("%d ч %d мин", $hours, $minutes);
        }

        // Вычисление общего времени заказа (2 → 7)
        if ($timeRow['start_trip'] && $timeRow['end_trip']) {
            $start = strtotime($timeRow['start_trip']);
            $end = strtotime($timeRow['end_trip']);
            $duration = $end - $start;
             // Вычитаем время паузы
            if (!empty($totalPauseTime)) {
                $duration = max(0, $duration - $totalPauseTime);
            }
            
            $hours = floor($duration / 3600);
            $minutes = floor(($duration % 3600) / 60);
            $tripDuration = sprintf("%d ч %d мин", $hours, $minutes);
        }

        // Получаем общее время паузы из pause_history
        $pauseQuery = $mysql->prepare("
        SELECT SUM(TIMESTAMPDIFF(SECOND, pause_time, resume_time)) AS total_pause_time
        FROM pause_history
        WHERE task_id = ?
        ");
        $pauseQuery->bind_param("i", $orderId);
        $pauseQuery->execute();
        $pauseResult = $pauseQuery->get_result();
        $pauseRow = $pauseResult->fetch_assoc();
        $totalPauseTime = $pauseRow['total_pause_time'] ?? 0; // Если NULL, то 0

        // Обновляем вставку в completetask
        $insertQuery = "INSERT INTO completetask (id, user_id, area, address, city, country, date, task, additional, worker_id, status, work_duration, total_pause_time, trip_duration) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mysql->prepare($insertQuery);
        $stmt->bind_param("iissssssssisss", 
        $order['id'], $order['user_id'], $order['area'], $order['address'], 
        $order['city'], $order['country'], $order['date'], $order['task'], 
        $order['additional'], $order['worker_id'], $action, 
        $workDuration, $totalPauseTime, $tripDuration);

        
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
