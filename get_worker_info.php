<?php
header('Content-Type: application/json');

$mysqli = new mysqli("localhost", "root", "", "balti24db");
if ($mysqli->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection error"]);
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo json_encode(["success" => false, "message" => "Incorrect ID"]);
    exit();
}

$query = $mysqli->prepare("SELECT id, name, surname, type, number FROM workers WHERE id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(["success" => true, "worker" => $row]);
} else {
    echo json_encode(["success" => false, "message" => "Employee not found"]);
}

$mysqli->close();
?>
