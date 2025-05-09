<?php
// form_act.php
// 1) Запрашивает идентификатор задачи (task_id)
// 2) Извлекает данные из таблицы `completetask` по primary key id
//    а также данные исполнителя из таблицы `workers`
// 3) Отображает форму с предзаполненными данными и полями для ввода оставшихся
// 4) Сохраняет данные в таблицу `info_akts`

// Подключение к БД
$mysqli = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysqli->connect_error) {
    die("Connection error: " . $mysqli->connect_error);
}

// Определяем шаг
$step = 'input';
if (isset($_GET['task_id'])) {
    $step = 'show';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['step'] ?? '') === 'save') {
    $step = 'save';
}
$error = '';
$task = null;

// Шаг show: получаем данные задачи и формируем массив $task
if ($step === 'show') {
    $task_id = intval($_GET['task_id']);
    if (!$task_id) {
        $error = 'Укажите идентификатор задачи';
    } else {
        $sql = "SELECT
                    id,
                    order_id,
                    user_id,
                    area,
                    phone,
                    address,
                    city,
                    country,
                    date,
                    task,
                    additional,
                    work_duration AS work_time,
                    trip_duration AS trip_time,
                    worker_id AS executor_id
                  FROM completetask
                 WHERE id = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param('i', $task_id);
        $stmt->execute();
        $task = $stmt->get_result()->fetch_assoc();
        if (!$task) {
            $error = "Задача с ID $task_id не найдена";
        } else {
            // Получаем данные исполнителя
            $stmt2 = $mysqli->prepare("SELECT name, id AS reg_number FROM workers WHERE id = ?");
            $stmt2->bind_param('i', $task['executor_id']);
            $stmt2->execute();
            $emp = $stmt2->get_result()->fetch_assoc();
            $task['executor_name'] = $emp['name'] ?? '';
            $task['executor_reg']  = $emp['reg_number'] ?? '';
            // Собираем site_address
            $task['site_address'] = "{$task['country']}, {$task['city']}, {$task['address']}";
        }
    }
}

// Шаг save: сохраняем данные из формы + скрытые поля в таблицу info_akts
if ($step === 'save') {
    $fields = [
        'task_id','order_id','user_id','area','phone','address','city','country','date','task','additional',
        'trip_time','work_time',
        'signature_date','client_name','client_reg','site_address',
        'work_description','materials','equipment_status','worker_count',
        'direct_costs','vat','total_with_vat','client_signature','executor_signature'
    ];
    $placeholders = implode(',', array_fill(0, count($fields), '?'));
    $cols = implode(',', $fields);
    $sql = "INSERT INTO info_akts ($cols) VALUES ($placeholders)";
    $stmt = $mysqli->prepare($sql);
    $types = '';
    $params = [];
    foreach ($fields as $f) {
        $val = $_POST[$f] ?? '';
        if ($f === 'worker_count') {
            $types .= 'i';
            $params[] = intval($val);
        } elseif (in_array($f, ['direct_costs','vat','total_with_vat'])) {
            $types .= 'd';
            $params[] = floatval($val);
        } else {
            $types .= 's';
            $params[] = $val;
        }
    }
    $stmt->bind_param($types, ...$params);
    if ($stmt->execute()) {
        echo '<p>Акт успешно сохранён!</p>';
        echo '<p><a href="form_act.php">Создать новый акт</a></p>';
        exit;
    } else {
        $error = 'Ошибка сохранения: ' . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Создание акта</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        label { display:block; margin: 8px 0; }
        input, textarea { width:300px; }
    </style>
</head>
<body>
<h1>Создание акта</h1>

<?php if ($step === 'input'): ?>
    <form method="get">
        <label>Идентификатор задачи (task_id):<br>
            <input type="number" name="task_id" required>
        </label>
        <button type="submit">Загрузить данные</button>
    </form>
<?php endif; ?>

<?php if ($error): ?>
    <p style="color:red;">&bull; <?=htmlspecialchars($error)?></p>
<?php endif; ?>

<?php if ($step === 'show' && $task): ?>
    <form method="post">
        <!-- скрытые поля -->
        <input type="hidden" name="task_id"       value="<?=htmlspecialchars($task['id'])?>">
        <input type="hidden" name="order_id"      value="<?=htmlspecialchars($task['order_id'])?>">
        <input type="hidden" name="user_id"       value="<?=htmlspecialchars($task['user_id'])?>">
        <input type="hidden" name="area"          value="<?=htmlspecialchars($task['area'])?>">
        <input type="hidden" name="phone"         value="<?=htmlspecialchars($task['phone'])?>">
        <input type="hidden" name="address"       value="<?=htmlspecialchars($task['address'])?>">
        <input type="hidden" name="city"          value="<?=htmlspecialchars($task['city'])?>">
        <input type="hidden" name="country"       value="<?=htmlspecialchars($task['country'])?>">
        <input type="hidden" name="date"          value="<?=htmlspecialchars($task['date'])?>">
        <input type="hidden" name="task"          value="<?=htmlspecialchars($task['task'])?>">
        <input type="hidden" name="additional"    value="<?=htmlspecialchars($task['additional'])?>">
        <input type="hidden" name="trip_time"     value="<?=htmlspecialchars($task['trip_time'])?>">
        <input type="hidden" name="work_time"     value="<?=htmlspecialchars($task['work_time'])?>">
        <input type="hidden" name="site_address"  value="<?=htmlspecialchars($task['site_address'])?>">
        <input type="hidden" name="executor_id"   value="<?=htmlspecialchars($task['executor_id'])?>">
        <input type="hidden" name="executor_name" value="<?=htmlspecialchars($task['executor_name'])?>">
        <input type="hidden" name="executor_reg"  value="<?=htmlspecialchars($task['executor_reg'])?>">

        <!-- видимые поля -->
        <label>Site Address:<br>
            <input type="text" name="site_address_disp" value="<?=htmlspecialchars($task['site_address'])?>" readonly>
        </label>
        <label>Additional (Причина):<br>
            <input type="text" name="additional_disp" value="<?=htmlspecialchars($task['additional'])?>" readonly>
        </label>
        <label>Job Type:<br>
            <input type="text" name="area_disp" value="<?=htmlspecialchars($task['area'])?>" readonly>
        </label>
        <label>Executor Name:<br>
            <input type="text" name="executor_name_disp" value="<?=htmlspecialchars($task['executor_name'])?>" readonly>
        </label>
        <label>Executor Reg:<br>
            <input type="text" name="executor_reg_disp" value="<?=htmlspecialchars($task['executor_reg'])?>" readonly>
        </label>
        <label>Trip Time:<br>
            <input type="text" name="trip_time_disp" value="<?=htmlspecialchars($task['trip_time'])?>" readonly>
        </label>
        <label>Work Time:<br>
            <input type="text" name="work_time_disp" value="<?=htmlspecialchars($task['work_time'])?>" readonly>
        </label>
        <label>Signature Date:<br>
            <input type="date" name="signature_date" required>
        </label>
        <label>Client Name:<br>
            <input type="text" name="client_name" required>
        </label>
        <label>Client Reg:<br>
            <input type="text" name="client_reg"></label>
        <label>Work Description:<br>
            <textarea name="work_description" required></textarea>
        </label>
        <label>Materials:<br>
            <textarea name="materials"></textarea>
        </label>
        <label>Equipment Status:<br>
            <input type="text" name="equipment_status"></label>
        <label>Worker Count:<br>
            <input type="number" name="worker_count" min="1"></label>
        <label>Direct Costs:<br>
            <input type="number" step="0.01" name="direct_costs"></label>
        <label>VAT:<br>
            <input type="number" step="0.01" name="vat"></label>
        <label>Total with VAT:<br>
            <input type="number" step="0.01" name="total_with_vat"></label>
        <label>Client Signature:<br>
            <input type="text" name="client_signature"></label>
        <label>Executor Signature:<br>
            <input type="text" name="executor_signature"></label>

        <input type="hidden" name="step" value="save">
        <button type="submit">Сохранить акт</button>
    </form>
<?php endif; ?>

</body>
</html>
