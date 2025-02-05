<?php
$mysql = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysql->connect_error) {
    die("Ошибка подключения: " . $mysql->connect_error);
}

$query = "SELECT * FROM tasks";
$result = $mysql->query($query);

echo "<table>";
echo "<tr><th>ID</th><th>User ID</th><th>Area</th><th>Phone</th><th>Address</th><th>City</th><th>Country</th><th>Date</th><th>Task</th><th>Additional</th><th>Worker</th><th>Status</th><th>Pause History</th><th>Action</th></tr>";

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

    echo "<tr style='{$rowStyle}'>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['user_id']}</td>";
    echo "<td>{$row['area']}</td>";
    echo "<td>{$row['phone']}</td>";
    echo "<td>{$row['address']}</td>";
    echo "<td>{$row['city']}</td>";
    echo "<td>{$row['country']}</td>";
    echo "<td>{$row['date']}</td>";
    echo "<td>{$row['task']}</td>";
    echo "<td>{$row['additional']}</td>";

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
    

    echo "</tr>";
}
echo "</table>";

$mysql->close();

?>
