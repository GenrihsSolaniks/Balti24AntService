<?php
$mysql = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysql->connect_error) {
    die("Ошибка подключения: " . $mysql->connect_error);
}

$query = "SELECT * FROM tasks";
$result = $mysql->query($query);

echo "<table>";
echo "<tr><th>ID</th><th>User ID</th><th>Area</th><th>Address</th><th>City</th><th>Country</th><th>Date</th><th>Task</th><th>Additional</th><th>Worker</th><th>Action</th></tr>";

while ($row = $result->fetch_assoc()) {
    // Окрашиваем фон в зависимости от статуса
    $rowClass = "";
    if ($row['status'] == 1) {
        $rowClass = "highlight-yellow"; // Назначен
    } elseif ($row['status'] == 2) {
        $rowClass = "highlight-green"; // Завершён
    }

    echo "<tr class='{$rowClass}'>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['user_id']}</td>";
    echo "<td>{$row['area']}</td>";
    echo "<td>{$row['address']}</td>";
    echo "<td>{$row['city']}</td>";
    echo "<td>{$row['country']}</td>";
    echo "<td>{$row['date']}</td>";
    echo "<td>{$row['task']}</td>";
    echo "<td>{$row['additional']}</td>";
    echo "<td>{$row['worker_id']}</td>";

    // Если заказ выполнен (статус 2) — убрать кнопку
    if ($row['status'] == 2) {
        echo "<td>✔ Завершён</td>";
    } else {
        // Кнопка назначения доступна только для заказов, у которых worker_id = NULL
        if ($row['worker_id'] === NULL) {
            echo "<td>
                <select id='worker-select-{$row['id']}'>";
            $workers = $mysql->query("SELECT id, name FROM workers");
            while ($worker = $workers->fetch_assoc()) {
                echo "<option value='{$worker['id']}'>{$worker['id']} - {$worker['name']}</option>";
            }
            echo "</select>
                <button onclick=\"assignWorker({$row['id']})\">Назначить</button>
            </td>";
        } else {
            echo "<td>⏳ В процессе</td>";
        }
    }

    echo "</tr>";
}
echo "</table>";


$mysql->close();
?>
