<?php
if (!isset($_COOKIE['admin_id'])) {
    header('Location: adminauth.html');
    exit();
}

$mysql = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysql->connect_error) {
    die("Connection error: " . $mysql->connect_error);
}

// Универсальная функция парсинга длительности
function parseDuration($duration) {
    if (preg_match('/(\d+)\s*[чh]\s*(\d+)\s*[мmin]/iu', $duration, $matches)) {
        return [(int)$matches[1], (int)$matches[2]];
    }
    return [0, 0];
}

// Получение завершённых заказов
$query = "SELECT id, user_id, area, address, city, country, date, task, additional, worker_id, 
                 work_duration, total_pause_time, trip_duration 
          FROM completetask";
$result = $mysql->query($query);

echo "<table border='1' cellpadding='5' cellspacing='0'>";
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
        <th>Work Time</th>
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

    // Получаем общее время паузы
    $totalPauseTime = !empty($row['total_pause_time']) ? $row['total_pause_time'] : 0;

    // Если нет, считаем вручную
    if ($totalPauseTime == 0) {
        $pauseQuery = $mysql->prepare("
            SELECT SUM(TIMESTAMPDIFF(SECOND, pause_time, resume_time)) AS total_pause
            FROM pause_history
            WHERE task_id = ? AND resume_time IS NOT NULL
        ");
        $pauseQuery->bind_param("i", $row['id']);
        $pauseQuery->execute();
        $pauseResult = $pauseQuery->get_result();
        $pauseRow = $pauseResult->fetch_assoc();
        $totalPauseTime = $pauseRow['total_pause'] ?? 0;
    }

    // ===== Работаем с Work Time =====
    $workDuration = "—";
    $totalWorkSeconds = 0;

    if (!empty($row['work_duration'])) {
        list($workHours, $workMinutes) = parseDuration($row['work_duration']);
        $totalWorkSeconds = ($workHours * 3600) + ($workMinutes * 60);

        // Вычитаем паузу
        if ($totalPauseTime > 0) {
            $totalWorkSeconds -= $totalPauseTime;
        }

        if ($totalWorkSeconds > 0) {
            $hours = floor($totalWorkSeconds / 3600);
            $minutes = floor(($totalWorkSeconds % 3600) / 60);
            $workDuration = "{$hours} h {$minutes} min";
        } else {
            $workDuration = "0 h 0 min";
        }
    }

    // ===== Работаем с Road Time =====
    $tripDuration = "—";
    $totalTripSeconds = 0;

    if (!empty($row['trip_duration'])) {
        list($tripHours, $tripMinutes) = parseDuration($row['trip_duration']);
        $totalTripSeconds = ($tripHours * 3600) + ($tripMinutes * 60);

        // Вычитаем рабочее время и паузы
        $roadSeconds = max(0, $totalTripSeconds - $totalWorkSeconds - $totalPauseTime);
        $roadHours = floor($roadSeconds / 3600);
        $roadMinutes = floor(($roadSeconds % 3600) / 60);
        $tripDuration = "{$roadHours} h {$roadMinutes} min";
    }

    echo "<td>{$workDuration}</td>";
    echo "<td>{$tripDuration}</td>";
    echo "</tr>";
}

echo "</table>";
$mysql->close();
?>
