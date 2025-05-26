<?php
require_once('libs/tcpdf/tcpdf.php');
$mysqli = new mysqli('localhost', 'root', '', 'balti24db');
$id = intval($_GET['id'] ?? 0);
if (!$id) die("Не указан ID акта");

$stmt = $mysqli->prepare("SELECT * FROM info_akts WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
if (!$data) die("Акт не найден");

$pdf = new TCPDF();
$pdf->SetCreator('Balti24');
$pdf->SetAuthor('Автоматическая система');
$pdf->SetTitle('Акт выполненных работ');
$pdf->SetFont('dejavusans', '', 12);
$pdf->AddPage();

// Вставка подписи, если есть
if (!empty($data['signature_image'])) {
    $imgData = str_replace('data:image/png;base64,', '', $data['signature_image']);
    $imgData = base64_decode($imgData);
    $signatureFile = 'temp_signatures/sign_' . time() . '.png';
    if (!is_dir('temp_signatures')) mkdir('temp_signatures', 0777, true);
    file_put_contents($signatureFile, $imgData);
    $pdf->Image($signatureFile, 130, 240, 90, 30);
    unlink($signatureFile);
}

// HTML-содержимое акта
$html = <<<HTML
<h2>Work Completion Report</h2>
<hr>
<p><strong>Task ID:</strong> {$data['task_id']}</p>
<p><strong>Signature Date:</strong> {$data['signature_date']}</p>
<p><strong>Client:</strong> {$data['client_name']} ({$data['client_reg']})</p>
<p><strong>Email:</strong> {$data['client_email']}</p>
<p><strong>Site Address:</strong> {$data['site_address']}</p>
<p><strong>Job Type:</strong> {$data['area']}</p>
<p><strong>Description of Work:</strong><br>{$data['work_description']}</p>
<p><strong>Materials Used:</strong> {$data['materials']}</p>
<p><strong>Equipment Status:</strong> {$data['equipment_status']}</p>
<p><strong>Number of Workers:</strong> {$data['worker_count']}</p>
<p><strong>Work Time:</strong> {$data['work_time']}</p>
<p><strong>Road Time:</strong> {$data['trip_time']}</p>
<p><strong>Costs:</strong> {$data['direct_costs']} + VAT {$data['vat']} = {$data['total_with_vat']}</p>
<p><strong>Executor:</strong> {$data['executor_name']} ({$data['executor_reg']})</p>
<p><strong>Client Signature (text):</strong> {$data['client_signature']}</p>
<p><strong>Executor Signature:</strong> {$data['executor_signature']}</p>
HTML;

$pdf->writeHTML($html);
$pdf->Output("akt_{$id}.pdf", 'I');
