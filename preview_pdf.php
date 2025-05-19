<?php
session_start();
require_once('libs/tcpdf/tcpdf.php');

// Проверка данных
if (!isset($_SESSION['akt_preview'])) {
    die("No preview data found.");
}
$data = $_SESSION['akt_preview'];

// PDF
$pdf = new TCPDF();
$pdf->SetCreator('Balti24');
$pdf->SetAuthor('Preview System');
$pdf->SetTitle('Preview Work Completion Report');
$pdf->SetFont('dejavusans', '', 12);
$pdf->AddPage();

// Контент
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
<p><strong>Costs:</strong> {$data['direct_costs']} + VAT {$data['vat']} = {$data['total_with_vat']}</p>
<p><strong>Executor:</strong> {$data['executor_name']} ({$data['executor_reg']})</p>
<p><strong>Client Signature:</strong> {$data['client_signature']}</p>
<p><strong>Executor Signature:</strong> {$data['executor_signature']}</p>
HTML;

$pdf->writeHTML($html);
$pdf->Output("preview_akt.pdf", 'I');
unset($_SESSION['akt_preview']);