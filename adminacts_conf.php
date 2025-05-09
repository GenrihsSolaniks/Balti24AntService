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
$query = "SELECT id, order_id, akta_id, file_name, file_data
          FROM acts";
$result = $mysql->query($query);

echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr>
        <th>ID</th>
        <th>order_id</th>
        <th>akta_id</th>
        <th>file_name</th>
        <th>file_data</th>
      </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['order_id']}</td>";
    echo "<td>{$row['akta_id']}</td>";
    echo "<td>{$row['file_name']}</td>";
    echo "<td>{$row['file_data']}</td>";
    echo "</tr>";
}

echo "</table>";
$mysql->close();
?>
