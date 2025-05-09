<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$database = "balti24db";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die(json_encode(["error" => "Ошибка подключения: " . $conn->connect_error]));
}

// Получаем занятые даты с типами работ
$sql = "SELECT date, type FROM employee_schedule";
$result = $conn->query($sql);

$busy_dates = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $date = $row['date'];
        $type = $row['type'];

        if (!isset($busy_dates[$date])) {
            $busy_dates[$date] = [];
        }
        if (!in_array($type, $busy_dates[$date])) {
            $busy_dates[$date][] = $type;
        }
    }
}

$conn->close();
echo json_encode($busy_dates);
?>
