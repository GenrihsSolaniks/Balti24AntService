<?php
require_once('libs/tcpdf/tcpdf.php');

// Подключение к базе
$mysqli = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysqli->connect_error) {
    die("Ошибка подключения к БД: " . $mysqli->connect_error);
}

// Получаем ID акта
$akt_id = intval($_GET['id'] ?? 0);
if (!$akt_id) {
    die("Не указан ID акта");
}

// Загружаем данные из info_akts
$stmt = $mysqli->prepare("SELECT * FROM info_akts WHERE id = ?");
$stmt->bind_param('i', $akt_id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
if (!$data) die("Акт не найден");

// Создаем PDF
$pdf = new TCPDF();
$pdf->SetCreator('Balti24');
$pdf->SetAuthor('Автоматическая система');
$pdf->SetTitle('Акт выполненных работ');
$pdf->SetFont('dejavusans', '', 12); // ВАЖНО! Добавляет поддержку русских букв
$pdf->AddPage();

// Контент PDF
$html = <<<HTML
<h2>Work Completion Report</h2>
<hr>
<p><strong>Task ID:</strong> {$data['task_id']}</p>
<p><strong>Signature Date:</strong> {$data['signature_date']}</p>
<p><strong>Client:</strong> {$data['client_name']} ({$data['client_reg']})</p>
<p><strong>Site Address:</strong> {$data['site_address']}</p>
<p><strong>Job Type:</strong> {$data['area']}</p>
<p><strong>Description of Work:</strong><br>{$data['work_description']}</p>
<p><strong>Materials Used:</strong> {$data['materials']}</p>
<p><strong>Equipment Status:</strong> {$data['equipment_status']}</p>
<p><strong>Number of Workers:</strong> {$data['worker_count']}</p>
<p><strong>Costs:</strong> {$data['direct_costs']} + VAT {$data['vat']} = {$data['total_with_vat']}</p>
<p><strong>Executor:</strong> {$data['executor_name']} ({$data['executor_reg']})</p>
<p><strong>Client Signature:</strong> {$data['client_signature']}</p>
<p><strong>Executor Signature:</strong> {$data['executor_signature']}</p>
HTML;

$pdf->writeHTML($html);
$pdf->Output("akt_{$akt_id}.pdf", 'D'); // 'I' — открыть в браузере
// 'D' — скачать файл
// 'F' — сохранить на сервере
// 'S' — вернуть как строку
