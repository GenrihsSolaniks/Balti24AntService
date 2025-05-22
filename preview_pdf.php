<?php
require_once('libs/tcpdf/tcpdf.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Нет данных для превью.");
}

$data = $_POST;

$pdf = new TCPDF();
$pdf->SetCreator('Balti24');
$pdf->SetAuthor('Preview System');
$pdf->SetTitle('Preview Work Completion Report');
$pdf->SetFont('dejavusans', '', 12);
$pdf->AddPage();

// Обработка подписи
if (!empty($data['signature_image'])) {
   $imgData = $data['signature_image'];
    $imgData = str_replace('data:image/png;base64,', '', $imgData);
    $imgData = base64_decode($imgData);
    $signatureFile = 'temp_signatures/sign_' . time() . '.png';

    if (!is_dir('temp_signatures')) {
        mkdir('temp_signatures', 0777, true);
    }

    // Сохраняем изображение без альфа-канала (прозрачности)
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

// Контент PDF
$html = <<<HTML
<h2>Work Completion Report (Preview)</h2>
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
<p><strong>Work Time:</strong> {$data['work_time']}</p>
<p><strong>Road Time:</strong> {$data['trip_time']}</p>
<p><strong>Costs:</strong> {$data['direct_costs']} + VAT {$data['vat']} = {$data['total_with_vat']}</p>
<p><strong>Executor:</strong> {$data['executor_name']} ({$data['executor_reg']})</p>
<p><strong>Client Signature:</strong> {$data['client_signature']}</p>
<p><strong>Executor Signature:</strong> {$data['executor_signature']}</p>
HTML;

$pdf->writeHTML($html);

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="preview_akt.pdf"');
$pdf->Output('preview_akt.pdf', 'I');
