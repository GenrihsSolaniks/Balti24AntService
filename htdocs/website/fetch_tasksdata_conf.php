<?php
$mysql = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysql->connect_error) {
    die("Ошибка подключения: " . $mysql->connect_error);
}

$query = "SELECT * FROM tasks";
$result = $mysql->query($query);

echo "<table>";
echo "<tr><th>ID</th><th>User ID</th><th>Area</th><th>Phone</th><th>Address</th><th>City</th><th>Country</th><th>Date</th><th>Task</th><th>Additional</th><th>Worker</th><th>Status</th><th>Action</th></tr>";

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
    // Устанавливаем цвет строки
    $rowStyle = $statusColors[$row['status']] ?? '';

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

    // Вывод имени работника
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

    // Вывод текстового статуса
    $statusDisplay = $statusText[$row['status']] ?? 'Неизвестный статус';
    echo "<td>{$statusDisplay}</td>";

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
