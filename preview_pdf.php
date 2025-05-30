<?php
require_once('libs/tcpdf/tcpdf.php');

session_start();

if (empty($_POST) && isset($_SESSION['akt_preview'])) {
    $data = $_SESSION['akt_preview'];
} elseif (!empty($_POST)) {
    $data = $_POST;
    $_SESSION['akt_preview'] = $_POST;
} else {
    die("Нет данных для превью.");
}

// === Очистка старых временных файлов подписи ===
$signatureDir = 'temp_signatures';
if (is_dir($signatureDir)) {
    $files = glob($signatureDir . '/*.png');
    foreach ($files as $file) {
        // Удаляем только если файл старше 5 минут
        if (is_file($file) && time() - filemtime($file) > 300) {
            unlink($file);
        }
    }
}

$pdf = new TCPDF();
$pdf->SetCreator('Balti24');
$pdf->SetAuthor('Preview System');
$pdf->SetTitle('Preview Work Completion Report');
$pdf->SetFont('dejavusans', '', 12);
$pdf->AddPage();

// Обработка подписи
if (!empty($data['signature_image'])) {
    $imgData = str_replace('data:image/png;base64,', '', $data['signature_image']);
    $imgData = base64_decode($imgData);
    $signatureFile = 'temp_signatures/sign_' . time() . '.png';

    if (!is_dir('temp_signatures')) {
        mkdir('temp_signatures', 0777, true);
    }

    $image = imagecreatefromstring($imgData);
    $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
    $white = imagecolorallocate($bg, 255, 255, 255);
    imagefill($bg, 0, 0, $white);
    imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
    imagepng($bg, $signatureFile);
    imagedestroy($image);
    imagedestroy($bg);

    $pdf->Image($signatureFile, 130, 240, 90, 30);
    unlink($signatureFile);
}

$html = <<<HTML
<h2>Work Completion Report (Preview)</h2>
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
<p><strong>Client Signature:</strong> {$data['client_signature']}</p>
<p><strong>Executor Signature:</strong> {$data['executor_signature']}</p>
HTML;

$pdf->writeHTML($html);
$pdf->Ln(10); // отступ после основного блока

// === Подтверждение клиента ===
if (!empty($data['payment_on_site']) && $data['payment_on_site'] == 1) {
    $pdf->writeHTML("<p><strong>Client Confirmation:</strong> Client paid on site.</p>");
} elseif (!empty($data['smart_id_confirmed'])) {
    $pdf->writeHTML("<p><strong>Client Confirmation:</strong> Smart-ID: {$data['smart_id_confirmed']}</p>");
} elseif (!empty($data['doc_front']) || !empty($data['doc_back'])) {
    $pdf->writeHTML("<p><strong>Client Confirmation:</strong> Verified by uploaded documents.</p>");
}

// === Фото выполненной работы ===
if (!empty($data['work_photo']) && file_exists($data['work_photo'])) {
    $pdf->AddPage();
    $pdf->writeHTML("<h3>Photo of Completed Work</h3>");
    $pdf->Image($data['work_photo'], '', '', 150);
}

// === Фотографии документов (для шаблона 3) ===
if (!empty($data['document_front']) && file_exists($data['document_front'])) {
    $pdf->AddPage();
    $pdf->writeHTML("<h3>Document Front</h3>");
    $pdf->Image($data['document_front'], '', '', 150);
}
if (!empty($data['document_back']) && file_exists($data['document_back'])) {
    $pdf->AddPage();
    $pdf->writeHTML("<h3>Document Back</h3>");
    $pdf->Image($data['document_back'], '', '', 150);
}
$pdf->Output("akt_{$id}.pdf", 'I');

