<?php
$pdo = new PDO("mysql:host=localhost;dbname=balti24db", "root", "");

$akta_id = $_GET['akta_id'] ?? '';
$stmt = $pdo->prepare("SELECT file_name, file_data FROM acts WHERE akta_id = ?");
$stmt->execute([$akta_id]);

if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename="' . $row['file_name'] . '"');
    echo $row['file_data'];
    exit;
} else {
    echo "Akts nav atrasts.";
}
?>
