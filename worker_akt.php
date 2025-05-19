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
        $stmt = $mysqli->prepare("
            SELECT id, order_id, user_id, area, phone, address, city, country,
                   date, task, additional, work_duration AS work_time,
                   trip_duration AS trip_time, worker_id AS executor_id
              FROM completetask
             WHERE id = ?
        ");
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
        } else {
            $error = "Задача не найдена.";
        }
    }
}

// === STEP: POST обработка ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $fields = [
        'task_id','order_id','user_id','area','phone','address','city','country','date','task','additional',
        'trip_time','work_time','signature_date','client_name','client_reg','site_address',
        'work_description','materials','equipment_status','worker_count',
        'direct_costs','vat','total_with_vat','client_signature','executor_signature',
        'executor_id','executor_name','executor_reg'
    ];

    $data = [];
    foreach ($fields as $f) {
        $data[$f] = $_POST[$f] ?? '';
    }

    // Кнопка предосмотра
    if ($action === 'preview') {
        $_SESSION['akt_preview'] = $data;
        header('Location: preview_pdf.php');
        exit;
    }

    // Кнопка сохранить
    if ($action === 'save') {
        $placeholders = implode(',', array_fill(0, count($fields), '?'));
        $columns = implode(',', $fields);
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
            $insert_id = $stmt->insert_id;
            header("Location: generate_pdf.php?id=$insert_id");
            exit;
        } else {
            $error = "Ошибка сохранения: " . $stmt->error;
        }
    }

    // Кнопка редактировать
    $task = $data;
    $step = 'show';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Work Act Form</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        label { display:block; margin: 8px 0; }
        input, textarea { width:300px; }
    </style>
</head>
<body>
<h1>Work Completion Act</h1>

<?php if ($step === 'input'): ?>
<form method="get">
    <label>Task ID:<br><input type="number" name="task_id" required></label>
    <button type="submit">Load Data</button>
</form>
<?php endif; ?>

<?php if ($error): ?>
<p style="color:red;">&bull; <?=htmlspecialchars($error)?></p>
<?php endif; ?>

<?php if ($step === 'show' && $task): ?>
<form method="post" target="_blank">
    <?php foreach ($task as $key => $val): ?>
        <input type="hidden" name="<?=htmlspecialchars($key)?>" value="<?=htmlspecialchars($val)?>">
    <?php endforeach; ?>
    <input type="hidden" name="step" value="save">

    <!-- READABLE FIELDS -->
    <label>Site Address:<br><input type="text" value="<?=htmlspecialchars($task['site_address'])?>" readonly></label>
    <label>Additional:<br><input type="text" value="<?=htmlspecialchars($task['additional'])?>" readonly></label>
    <label>Job Type:<br><input type="text" value="<?=htmlspecialchars($task['area'])?>" readonly></label>
    <label>Executor Name:<br><input type="text" value="<?=htmlspecialchars($task['executor_name'])?>" readonly></label>
    <label>Executor Reg:<br><input type="text" value="<?=htmlspecialchars($task['executor_reg'])?>" readonly></label>
    <label>Trip Time:<br><input type="text" value="<?=htmlspecialchars($task['trip_time'])?>" readonly></label>
    <label>Work Time:<br><input type="text" value="<?=htmlspecialchars($task['work_time'])?>" readonly></label>

    <!-- EDITABLE FIELDS -->
    <label>Signature Date:<br><input type="date" name="signature_date" value="<?=htmlspecialchars($task['signature_date'] ?? '')?>" required></label>
    <label>Client Name:<br><input type="text" name="client_name" value="<?=htmlspecialchars($task['client_name'] ?? '')?>" required></label>
    <label>Client Reg:<br><input type="text" name="client_reg" value="<?=htmlspecialchars($task['client_reg'] ?? '')?>"></label>
    <label>Work Description:<br><textarea name="work_description" required><?=htmlspecialchars($task['work_description'] ?? '')?></textarea></label>
    <label>Materials:<br><textarea name="materials"><?=htmlspecialchars($task['materials'] ?? '')?></textarea></label>
    <label>Equipment Status:<br><input type="text" name="equipment_status" value="<?=htmlspecialchars($task['equipment_status'] ?? '')?>"></label>
    <label>Worker Count:<br><input type="number" name="worker_count" min="1" value="<?=htmlspecialchars($task['worker_count'] ?? '')?>"></label>
    <label>Direct Costs:<br><input type="number" step="0.01" name="direct_costs" value="<?=htmlspecialchars($task['direct_costs'] ?? '')?>"></label>
    <label>VAT:<br><input type="number" step="0.01" name="vat" value="<?=htmlspecialchars($task['vat'] ?? '')?>"></label>
    <label>Total with VAT:<br><input type="number" step="0.01" name="total_with_vat" value="<?=htmlspecialchars($task['total_with_vat'] ?? '')?>"></label>
    <label>Client Signature:<br><input type="text" name="client_signature" value="<?=htmlspecialchars($task['client_signature'] ?? '')?>"></label>
    <label>Executor Signature:<br><input type="text" name="executor_signature" value="<?=htmlspecialchars($task['executor_signature'] ?? '')?>"></label>

    <!-- ACTION BUTTONS -->
    <button type="submit" name="action" value="preview">🔍 Preview PDF</button>
    <button type="submit" name="action" value="edit">✏️ Edit</button>
    <button type="submit" name="action" value="save">💾 Save & Download</button>
</form>
<?php endif; ?>
</body>
</html>
