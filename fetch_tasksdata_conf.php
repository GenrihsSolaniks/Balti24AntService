<?php
$mysql = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysql->connect_error) {
    die("Ошибка подключения: " . $mysql->connect_error);
}

$query = "SELECT * FROM tasks";
$result = $mysql->query($query);

echo "<table>";
echo "<tr><th>ID</th><th>User ID</th><th>Area</th><th>Phone</th><th>Address</th><th>City</th><th>Country</th><th>Date</th><th>Task</th><th>Additional</th><th>Photo</th><th>Worker</th><th>Status</th><th>Pause History</th><th>Action</th><th>Execution Time</th><th>Work Time</th><th>Road Time</th></tr>";

// Определяем цвета для каждого статуса
$statusColors = [
    0 => '',                // Создан (по умолчанию белый)
    1 => 'background: #fff3cd;', // Ожидание принятия (светло-жёлтый)
    2 => 'background: #ffeeba;', // Выехал (жёлтый)
    3 => 'background: #ffc107;', // Приехал к клиенту (темно-жёлтый)
    4 => 'background: #ff851b;', // Приступил к работе (оранжевый)
    5 => 'background: #f0ad4e;', // Завершил работу (светло-красный)
    6 => 'background: #d4edda;', // Выехал обратно (светло-зелёный)
    7 => 'background: #5cb85c;',  // Приехал на базу (завершён)
    8 => 'background:rgb(7, 145, 7);'  // Потвеждаю выполнение заказа
];

// Определяем текстовое описание для каждого статуса
$statusText = [
    0 => 'Создан',
    1 => 'Ожидание принятия',
    2 => 'Выехал',
    3 => 'Приехал к клиенту',
    4 => 'Приступил к работе',
    5 => 'Завершил работу',
    6 => 'Выехал обратно',
    7 => 'Приехал на базу',
    8 => 'Потвеждаю выполнение заказа'
];

while ($row = $result->fetch_assoc()) {
    $rowStyle = $statusColors[$row['status']] ?? '';

    // Если пауза включена, перекрашиваем строку в красный и добавляем текст
    if ($row['pause_status'] == 1) {
        $rowStyle = 'background: #ff4d4d;';
        $pauseDisplay = 'На паузе';
    } else {
        $pauseDisplay = 'Активно';
    }

    $pauseHistoryQuery = $mysql->prepare("SELECT pause_time, resume_time FROM pause_history WHERE task_id = ?");
    $pauseHistoryQuery->bind_param("i", $row['id']);
    $pauseHistoryQuery->execute();
    $pauseHistoryResult = $pauseHistoryQuery->get_result();

    // Формируем строки для истории пауз
    $pauseHistoryDisplay = '';
    while ($historyRow = $pauseHistoryResult->fetch_assoc()) {
        $resumeTime = $historyRow['resume_time'] ?? 'Текущая пауза';
        $pauseHistoryDisplay .= "Пауза с {$historyRow['pause_time']} до {$resumeTime}<br>";
    }

    // Получаем первое и последнее время изменения статуса
    $timeQuery = $mysql->prepare("
    SELECT MIN(timestamp) AS start_time, MAX(timestamp) AS end_time FROM task_timestamps WHERE task_id = ?");
    $timeQuery->bind_param("i", $row['id']);
    $timeQuery->execute();
    $timeResult = $timeQuery->get_result();
    $timeRow = $timeResult->fetch_assoc();

    // Вычисляем разницу во времени
    $executionTime = "—";
    if (!empty($timeRow['start_time']) && !empty($timeRow['end_time'])) {
    $start = new DateTime($timeRow['start_time']);
    $end = new DateTime($timeRow['end_time']);
    $diff = $start->diff($end);
    $executionTime = "{$diff->h} ч. {$diff->i} мин.";
    }

    echo "<tr style='{$rowStyle}'>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['user_id']}</td>";
    echo "<td>{$row['area']}</td>";
    echo "<td><a href='https://wa.me/{$row['phone']}' target='_blank'>{$row['phone']}</a></td>";
    echo "<td>{$row['address']}</td>";
    echo "<td>{$row['city']}</td>";
    echo "<td>{$row['country']}</td>";
    echo "<td>{$row['date']}</td>";
    echo "<td>{$row['task']}</td>";
    echo "<td>{$row['additional']}</td>";

    // Выводим фото, если оно есть
    if (!empty($row['photo_path']) && file_exists($row['photo_path'])) {
        echo "<td>
        <a href='{$row['photo_path']}' target='_blank'>
            <img src='{$row['photo_path']}' alt='Фото' style='max-width:100px; max-height:100px;'>
        </a>
      </td>";
    } else {
        echo "<td>—</td>";
    }

    // Имя работника
    if (!empty($row['worker_id'])) {
        $workerQuery = $mysql->prepare("SELECT name FROM workers WHERE id = ?");
        $workerQuery->bind_param("i", $row['worker_id']);
        $workerQuery->execute();
        $workerResult = $workerQuery->get_result();
        $workerName = $workerResult->fetch_assoc()['name'] ?? 'Неизвестно';
        echo "<td>{$workerName}</td>";
    } else {
        echo "<td>Не назначен</td>";
    }

    // Статус и пауза
    $statusDisplay = $statusText[$row['status']] ?? 'Неизвестный статус';
    echo "<td>{$statusDisplay}</td>";
    //echo "<td>{$statusDisplay} ({$pauseDisplay})</td>";

    // Вывод истории пауз
    echo "<td>{$pauseHistoryDisplay}</td>";

    // Условие для вывода кнопки назначения работника
    if (empty($row['worker_id'])) {
        echo "<td>
            <select id='worker-select-{$row['id']}'>";
        $workers = $mysql->query("SELECT id, name FROM workers WHERE type = '{$row['area']}'");
        while ($worker = $workers->fetch_assoc()) {
            echo "<option value='{$worker['id']}'>{$worker['id']} - {$worker['name']}</option>";
        }
        echo "</select>
            <button onclick=\"assignWorker({$row['id']})\">Назначить</button>
        </td>";
    } else {
        echo "<td>⏳ В процессе</td>";
    }
    
    echo "<td>{$executionTime}</td>";

        // Получаем время начала и окончания работы (4 → 5) и общее время заказа (2 → 7)
    $timeQuery = $mysql->prepare("
    SELECT 
        MAX(CASE WHEN status = 2 THEN timestamp END) AS start_trip,
        MAX(CASE WHEN status = 4 THEN timestamp END) AS start_work,
        MAX(CASE WHEN status = 5 THEN timestamp END) AS end_work,
        MAX(CASE WHEN status = 7 THEN timestamp END) AS end_trip
    FROM task_timestamps
    WHERE task_id = ?
    ");
        $timeQuery->bind_param("i", $row['id']);
        $timeQuery->execute();
        $timeResult = $timeQuery->get_result();
        $timeRow = $timeResult->fetch_assoc();

        $workDuration = '—'; // Время работы (4 → 5)
        $tripDuration = '—'; // Общее время заказа (2 → 7)

    if ($timeRow['start_work'] && $timeRow['end_work']) {
        $start = strtotime($timeRow['start_work']);
        $end = strtotime($timeRow['end_work']);
        $duration = $end - $start;
        $hours = floor($duration / 3600);
        $minutes = floor(($duration % 3600) / 60);
        $workDuration = sprintf("%d ч %d мин", $hours, $minutes);
    }

    if ($timeRow['start_trip'] && $timeRow['end_trip']) {
        $start = strtotime($timeRow['start_trip']);
        $end = strtotime($timeRow['end_trip']);
        $duration = $end - $start;
        $hours = floor($duration / 3600);
        $minutes = floor(($duration % 3600) / 60);
        $tripDuration = sprintf("%d ч %d мин", $hours, $minutes);
    }

    // Добавляем два новых столбца
    echo "<td>{$workDuration}</td>";
    echo "<td>{$tripDuration}</td>";

        echo "</tr>";
    }


// ===== Показ проблемных заказов =====
$problemQuery = "SELECT * FROM problematic_tasks";
$problemResult = $mysql->query($problemQuery);

while ($row = $problemResult->fetch_assoc()) {
    echo "<tr style='background-color: #ffcccc;'>"; // красный фон

    echo "<td>{$row['id']} ⚠️</td>";
    echo "<td>{$row['user_id']}</td>";
    echo "<td>{$row['area']}</td>";
    echo "<td><a href='https://wa.me/{$row['phone']}' target='_blank'>{$row['phone']}</a></td>";
    echo "<td>{$row['address']}</td>";
    echo "<td>{$row['city']}</td>";
    echo "<td>{$row['country']}</td>";
    echo "<td>{$row['date']}</td>";
    echo "<td>{$row['task']}</td>";
    echo "<td>{$row['additional']}</td>";

    // Фото
    if (!empty($row['photo_path']) && file_exists($row['photo_path'])) {
        echo "<td>
        <a href='{$row['photo_path']}' target='_blank'>
            <img src='{$row['photo_path']}' alt='Фото' style='max-width:100px; max-height:100px;'>
        </a>
      </td>";
    } else {
        echo "<td>—</td>";
    }

    // Имя работника
    if (!empty($row['worker_id'])) {
        $workerQuery = $mysql->prepare("SELECT name FROM workers WHERE id = ?");
        $workerQuery->bind_param("i", $row['worker_id']);
        $workerQuery->execute();
        $workerResult = $workerQuery->get_result();
        $workerName = $workerResult->fetch_assoc()['name'] ?? 'Неизвестно';
        echo "<td>{$workerName}</td>";
    } else {
        echo "<td>Не назначен</td>";
    }

    // Статус
    echo "<td><b style='color: red;'>Проблемный (250)</b></td>";

    // История пауз
    echo "<td>—</td>";

    // Действия
    echo "<td><i>Требует проверки</i></td>";

    // Время выполнения / работы / дороги — пока не считаем
    echo "<td>—</td>";
    echo "<td>—</td>";
    echo "<td>—</td>";

}
echo "</table>";

$mysql->close();

?>