<?php
session_start();
$mysqli = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

$error = '';
$step = 'input';
$task = null;

// === STEP: Показ по task_id ===
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['task_id'])) {
    $step = 'show';
    $task_id = intval($_GET['task_id']);
    if ($task_id <= 0) {
        $error = "Некорректный task_id";
    } else {
        $stmt = $mysqli->prepare("SELECT id, order_id, user_id, area, phone, email, address, city, country,
                date, task, additional, work_duration AS work_time,
                trip_duration AS trip_time, worker_id AS executor_id
            FROM completetask WHERE id = ?");
        $stmt->bind_param('i', $task_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $task = $result->fetch_assoc();

        if ($task) {
            $task['task_id'] = $task['id'];
            $task['trip_time'] = substr($task['trip_time'], 0, 19);
            $task['work_time'] = substr($task['work_time'], 0, 19);

            $stmt2 = $mysqli->prepare("SELECT name, id AS reg_number FROM workers WHERE id = ?");
            $stmt2->bind_param('i', $task['executor_id']);
            $stmt2->execute();
            $emp = $stmt2->get_result()->fetch_assoc();
            $task['executor_name'] = $emp['name'] ?? '';
            $task['executor_reg']  = $emp['reg_number'] ?? '';
            $task['site_address'] = "{$task['country']}, {$task['city']}, {$task['address']}";
            $task['client_email'] = $task['email'];
            $task['payment_on_site'] = '1'; // по умолчанию
        } else {
            $error = "Задача не найдена.";
        }
    }
}

// === STEP: POST ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $fields = [
        'task_id','order_id','user_id','area','phone','address','city','country','date','task','additional',
        'trip_time','work_time','signature_date','client_name','client_reg','client_email','site_address',
        'work_description','materials','equipment_status','worker_count',
        'direct_costs','vat','total_with_vat','client_signature','executor_signature',
        'executor_id','executor_name','executor_reg',
        'signature_image', 'payment_on_site', 'work_photos'
    ];

    $data = [];
    foreach ($fields as $f) {
        if ($f === 'payment_on_site') {
            $data[$f] = isset($_POST[$f]) ? 1 : 0;
        } else {
            $data[$f] = $_POST[$f] ?? '';
        }       
    }

    // === Обработка нескольких фото работ ===
    $photo_paths = [];
    $upload_dir = 'uploads/';
    if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

    if (!empty($_FILES['work_photos']['name'][0])) {
        foreach ($_FILES['work_photos']['tmp_name'] as $i => $tmp_name) {
            $name = basename($_FILES['work_photos']['name'][$i]);
            $target = $upload_dir . time() . "_" . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $name);
            if (move_uploaded_file($tmp_name, $target)) {
                $photo_paths[] = $target;
            }
        }
    }
    $data['work_photos'] = implode(',', $photo_paths); // Сохраняем как строку


    // Кнопка предосмотра
    if ($action === 'preview') {
        $_SESSION['akt_preview'] = $data;
        $_SESSION['template_type'] = 1;
        header('Location: preview_pdf.php');
        exit;
    }

    // Кнопка сохранить
    if ($action === 'save') {
        $columns = implode(',', $fields);
        $placeholders = implode(',', array_fill(0, count($fields), '?'));
        $stmt = $mysqli->prepare("INSERT INTO info_akts ($columns) VALUES ($placeholders)");

        $types = '';
        $params = [];
        foreach ($fields as $f) {
            if (in_array($f, ['worker_count', 'executor_id'])) {
                $types .= 'i'; $params[] = (int)$data[$f];
            } elseif (in_array($f, ['direct_costs','vat','total_with_vat'])) {
                $types .= 'd'; $params[] = (float)$data[$f];
            } else {
                $types .= 's'; $params[] = $data[$f];
            }
        }
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $step = 'saved';
        } else {
            $error = "Ошибка сохранения: " . $stmt->error;
        }
    }

    $task = $data;
    $step = 'show';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Work Completion Report — Template 1 (Paid on Site)</title>
  <style>label{display:block;margin:8px 0;}input,textarea{width:300px;}</style>
</head>
<body>
    <h1>Work Completion Act</h1>
<?php if ($step === 'input'): ?>
<form method="get">
  <label>Task ID:<br><input type="number" name="task_id" required></label>
  <button type="submit">Load</button>
</form>
<?php endif; ?>

<?php if ($error): ?><p style="color:red;"><?=htmlspecialchars($error)?></p><?php endif; ?>

<?php if ($step === 'show' && $task): ?>
<form method="post" enctype="multipart/form-data" id="aktForm">
  <?php foreach ($task as $key => $val): ?>
    <input type="hidden" name="<?=htmlspecialchars($key)?>" value="<?=htmlspecialchars($val)?>">
  <?php endforeach; ?>
  <input type="hidden" name="step" value="save">

  <h1>Work Completion Report — Template 1 (Paid on Site)</h1>

<label>Site Address:<br><input type="text" value="<?=htmlspecialchars($task['site_address'])?>" readonly></label>
<label>Additional Info:<br><input type="text" value="<?=htmlspecialchars($task['additional'])?>" readonly></label>
<label>Job Type:<br><input type="text" value="<?=htmlspecialchars($task['area'])?>" readonly></label>
<label>Executor Name:<br><input type="text" value="<?=htmlspecialchars($task['executor_name'])?>" readonly></label>
<label>Executor ID:<br><input type="text" value="<?=htmlspecialchars($task['executor_reg'])?>" readonly></label>
<label>Trip Time:<br><input type="text" value="<?=htmlspecialchars($task['trip_time'])?>" readonly></label>
<label>Work Time:<br><input type="text" value="<?=htmlspecialchars($task['work_time'])?>" readonly></label>

<label>Signature Date:<br><input type="date" name="signature_date" value="<?=htmlspecialchars($task['signature_date'] ?? '')?>" required></label>
<label>Client Name:<br><input type="text" name="client_name" value="<?=htmlspecialchars($task['client_name'] ?? '')?>" required></label>
<label>Client Email:<br><input type="email" name="client_email" value="<?=htmlspecialchars($task['client_email'] ?? '')?>" readonly></label>
<label>Client ID:<br><input type="text" name="client_reg" value="<?=htmlspecialchars($task['client_reg'] ?? '')?>"></label>
<label>Work Description:<br><textarea name="work_description" required><?=htmlspecialchars($task['work_description'] ?? '')?></textarea></label>
<label>Materials:<br><textarea name="materials"><?=htmlspecialchars($task['materials'] ?? '')?></textarea></label>
<label>Equipment Status:<br><input type="text" name="equipment_status" value="<?=htmlspecialchars($task['equipment_status'] ?? '')?>"></label>
<label>Workers involved:<br><input type="number" name="worker_count" value="<?=htmlspecialchars($task['worker_count'] ?? 1)?>" min="1"></label>
<label>Direct Costs:<br><input type="number" step="0.01" name="direct_costs" value="<?=htmlspecialchars($task['direct_costs'] ?? '')?>"></label>
<label>VAT:<br><input type="number" step="0.01" name="vat" value="<?=htmlspecialchars($task['vat'] ?? '')?>"></label>
<label>Total with VAT:<br><input type="number" step="0.01" name="total_with_vat" value="<?=htmlspecialchars($task['total_with_vat'] ?? '')?>"></label>
<label>Client Signature (text):<br><input type="text" name="client_signature" value="<?=htmlspecialchars($task['client_signature'] ?? '')?>"></label>
<label>
    <div style="display: flex; align-items: center; gap: 8px;">
        <input type="checkbox" checked disabled>
        <span>
            Client confirmed payment on site <small>(auto-included in PDF)</small>
        </span>
    </div>
</label>
<input type="hidden" name="payment_on_site" value="1">
<label>Photos of completed work:<br>
  <input type="file" name="work_photos[]" accept="image/*" multiple>
</label>

<label>Executor Signature: <br><input type="text" name="executor_signature" value="<?=htmlspecialchars($task['executor_signature'] ?? '')?>"></label>
<br><br>
<!--<button type="button" onclick="submitPreview()">🔍 Предпросмотр PDF</button>-->
<button type="submit" name="action" value="save">💾 Сохранить</button>
</form>
<?php endif; ?>

<script>
let canvas = document.getElementById("signature-pad");
let ctx = canvas.getContext("2d");
let drawing = false;

function captureSignature() {
    const signatureData = canvas.toDataURL("image/png");
    document.getElementById("signature_image").value = signatureData;
}

document.querySelector("form").addEventListener("submit", captureSignature);
canvas.addEventListener("mousedown", () => { drawing = true; ctx.beginPath(); });
canvas.addEventListener("mouseup", () => drawing = false);
canvas.addEventListener("mousemove", e => {
    if (!drawing) return;
    ctx.lineWidth = 2; ctx.lineCap = "round";
    ctx.lineTo(e.offsetX, e.offsetY); ctx.stroke();
});
function clearSignature() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}
function submitPreview() {
    captureSignature();
    const form = document.getElementById('aktForm');
    const previewWindow = window.open('', 'pdfPreview');
    const oldTarget = form.target;
    form.target = 'pdfPreview';

    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'action';
    hiddenInput.value = 'preview';
    form.appendChild(hiddenInput);

    form.submit();
    form.target = oldTarget;
    hiddenInput.remove();
}
</script>
</body>
</html>
