<?php
if (!isset($_COOKIE['worker_id'])) {
    die("Ошибка: worker_id не задан");
}

$workerId = (int)$_COOKIE['worker_id'];

$mysql = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysql->connect_error) {
    die("Ошибка подключения: " . $mysql->connect_error);
}

// Выбираем задачи, назначенные данному работнику, со статусом меньше 7
$query = "SELECT * FROM tasks WHERE worker_id = ? AND status < 7";
$stmt = $mysql->prepare($query);
$stmt->bind_param("i", $workerId);
$stmt->execute();
$result = $stmt->get_result();

echo "<table>";
echo "<tr><th>ID</th><th>Area</th><th>Address</th><th>Task</th><th>Status</th><th>Action</th></tr>";

$statusNames = [
    1 => 'Ожидание принятия',
    2 => 'Выехал',
    3 => 'Приехал к клиенту',
    4 => 'Приступил к работе',
    5 => 'Завершил работу',
    6 => 'Выехал обратно',
    7 => 'Приехал на базу'
];

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['area']}</td>";
    echo "<td>{$row['address']}</td>";
    echo "<td>{$row['task']}</td>";
    echo "<td>{$statusNames[$row['status']]}</td>";

    // Обработка действий на статусе 4 (Приступил к работе)
    if ($row['status'] == 4) {
        if ($row['pause_status'] == 1) {
            // Если пауза активна, показываем кнопку "Снять паузу"
            echo "<td><button onclick=\"togglePauseStatus({$row['id']})\">Снять паузу</button></td>";
        } else {
            // Если пауза не активна, показываем две кнопки
            $nextStatus = $row['status'] + 1;
            echo "<td>
                <button onclick=\"togglePauseStatus({$row['id']})\">Поставить на паузу</button>
                <button onclick=\"updateOrderStatus({$row['id']}, {$nextStatus})\">{$statusNames[$nextStatus]}</button>
            </td>";
        }
    } elseif ($row['status'] == 5) {
         echo "<td>
        <input type=\"checkbox\" id=\"confirmCheck{$row['id']}\" onchange=\"toggleButton{$row['id']}()\">
        <label for=\"confirmCheck{$row['id']}\"> Клиент согласен</label><br>
        <button id=\"departButton{$row['id']}\" onclick=\"updateOrderStatus({$row['id']}, 6)\" disabled>Выехал обратно</button>

        <script>
            function toggleButton{$row['id']}() {
                const checkbox = document.getElementById('confirmCheck{$row['id']}');
                const button = document.getElementById('departButton{$row['id']}');
                button.disabled = !checkbox.checked;
            }
        </script>
    </td>";
    } elseif ($row['status'] == 6) {
        // Если статус 6 (Выехал обратно), показываем кнопку "Приехал на базу"
        echo "<td><button onclick=\"updateOrderStatus({$row['id']}, 7)\">Приехал на базу</button></td>";
    } elseif ($row['status'] < 7) {
        // Остальные переходы
        $nextStatus = $row['status'] + 1;
        echo "<td><button onclick=\"updateOrderStatus({$row['id']}, {$nextStatus})\">{$statusNames[$nextStatus]}</button></td>";
    } else {
        echo "<td>✔ Завершён</td>";
    }
    // Если статус 7 (Завершён), показываем только текст    

    echo "</tr>";
}
echo "</table>";

$mysql->close();

?>
