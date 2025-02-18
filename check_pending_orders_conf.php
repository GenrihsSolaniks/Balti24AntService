<?php
$mysql = new mysqli('localhost', 'root', '', 'balti24db');

$query = "SELECT * FROM tasks WHERE status = 1 AND TIMESTAMPDIFF(MINUTE, date, NOW()) > 30";
$result = $mysql->query($query);

if ($result->num_rows > 0) {
    echo json_encode(['alert' => true]);
} else {
    echo json_encode(['alert' => false]);
}

$mysql->close();
?>
