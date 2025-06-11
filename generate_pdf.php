<?php
require_once('libs/tcpdf/tcpdf.php');
$mysqli = new mysqli('localhost', 'root', '', 'balti24db');
$id = intval($_GET['id'] ?? 0);
if (!$id) die("No act ID specified");

$stmt = $mysqli->prepare("SELECT * FROM info_akts WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
if (!$data) die("Акт не найден");

$pdf = new TCPDF();
$pdf->SetCreator('Balti24');
$pdf->SetAuthor('Automatic system');
$pdf->SetTitle('Acts of Completion');
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
$html .= "<p><strong>Client Confirmation:</strong> ";
if ($data['payment_on_site'] == 1) {
    $html .= "Client paid on site.";
} elseif (!empty($data['smart_id_confirmed'])) {
    $html .= "Smart-ID: {$data['smart_id_confirmed']}";
} elseif (!empty($data['document_front']) || !empty($data['document_back'])) {
    $html .= "Verified by uploaded documents.";
}
$html .= "</p>";
$pdf->writeHTML($html);
$pdf->Ln(10);

// === Множественные фото работы ===
if (!empty($data['work_photos'])) {
    $photos = explode(',', $data['work_photos']);
    foreach ($photos as $photoPath) {
        if (file_exists($photoPath)) {
            $pdf->AddPage();
            $pdf->writeHTML("<h3>Photo of Completed Work</h3>");
            $pdf->Image($photoPath, '', '', 150);
        }
    }
}


// === Старое поле "одно фото работы", если нужно для совместимости ===
if (!empty($data['work_photo']) && file_exists($data['work_photo'])) {
    $pdf->AddPage();
    $pdf->writeHTML("<h3>Single Work Photo</h3>");
    $pdf->Ln(5);
    $pdf->Image($data['work_photo'], '', '', 150);
}

// === Фото документов клиента ===
if (!empty($data['document_front']) && file_exists($data['document_front'])) {
    $pdf->AddPage();
    $pdf->writeHTML("<h3>Document Front</h3>");
    $pdf->Ln(5);
    $pdf->Image($data['document_front'], '', '', 150);
}
if (!empty($data['document_back']) && file_exists($data['document_back'])) {
    $pdf->AddPage();
    $pdf->writeHTML("<h3>Document Back</h3>");
    $pdf->Ln(5);
    $pdf->Image($data['document_back'], '', '', 150);
}

$pdf->Output("akt_{$id}.pdf", 'I');
