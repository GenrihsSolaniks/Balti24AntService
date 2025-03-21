<?php
if (!isset($_COOKIE['admin_id'])) {
    header('Location: adminauth.html');
    exit();
}

$mysql = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysql->connect_error) {
    die("Connection error: " . $mysql->connect_error);
}

// Запрос на получение выполненных заказов
$query = "SELECT id, user_id, area, address, city, country, date, task, additional, worker_id, 
                 work_duration, total_pause_time, trip_duration 
          FROM completetask";
$result = $mysql->query($query);

echo "<table>";
echo "<tr>
        <th>ID</th>
        <th>User ID</th>
        <th>Area</th>
        <th>Address</th>
        <th>City</th>
        <th>Country</th>
        <th>Date</th>
        <th>Task</th>
        <th>Additional</th>
        <th>Worker ID</th>
        <th>Work Time</th> <!-- Уже с учетом паузы -->
        <th>Road Time</th>
    </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
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

    // Получаем суммарное время паузы
    $totalPauseTime = !empty($row['total_pause_time']) ? $row['total_pause_time'] : 0;

    // Если total_pause_time отсутствует, считаем из pause_history
    if ($totalPauseTime == 0) {
        $pauseQuery = $mysql->prepare("
            SELECT SUM(TIMESTAMPDIFF(SECOND, pause_time, resume_time)) AS total_pause
            FROM pause_history
            WHERE task_id = ?
            AND resume_time IS NOT NULL
        ");
        $pauseQuery->bind_param("i", $row['id']);
        $pauseQuery->execute();
        $pauseResult = $pauseQuery->get_result();
        $pauseRow = $pauseResult->fetch_assoc();
        $totalPauseTime = $pauseRow['total_pause'] ?? 0;
    }
 
// Вычисляем Work Time (уже с учетом вычета паузы)
$workDuration = "—";
$totalWorkSeconds = 0;
if (!empty($row['work_duration'])) {
    list($workHours, $workMinutes) = sscanf($row['work_duration'], "%d h %d min");
    $totalWorkSeconds = ($workHours * 3600) + ($workMinutes * 60);
    
    // Вычитаем паузу
    if (!empty($row['total_pause_time']) && $row['total_pause_time'] > 0) {
        $totalWorkSeconds -= $row['total_pause_time'];
    }
    
    if ($totalWorkSeconds > 0) {
        $workHours = floor($totalWorkSeconds / 3600);
        $workMinutes = floor(($totalWorkSeconds % 3600) / 60);
        $workDuration = sprintf("%d h %d min", $workHours, $workMinutes);
    } else {
        $workDuration = "0 h 0 min";
    }
}

// Вычисляем Road Time (общее время - рабочее время - паузы)
$tripDuration = "—";
$totalTripSeconds = 0;
if (!empty($row['trip_duration'])) {
    list($tripHours, $tripMinutes) = sscanf($row['trip_duration'], "%d h %d min");
    $totalTripSeconds = ($tripHours * 3600) + ($tripMinutes * 60);

    // Вычитаем рабочее время и паузы из общего
    $remainingSeconds = max(0, $totalTripSeconds - $totalWorkSeconds - $totalPauseTime);
    $tripHours = floor($remainingSeconds / 3600);
    $tripMinutes = floor(($remainingSeconds % 3600) / 60);
    $tripDuration = sprintf("%d h %d min", $tripHours, $tripMinutes);
}

// Вывод данных в таблицу
echo "<td>{$workDuration}</td>";  // Work Time
echo "<td>{$tripDuration}</td>";  // Road Time

    
    echo "</tr>";
}
echo "</table>";

$mysql->close();
?>
