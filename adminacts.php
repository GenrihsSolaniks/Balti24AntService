<?php
if (!isset($_COOKIE['admin_id'])) {
    header('Location: adminauth.html');
    exit();
}

$mysqli = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

$result = $mysqli->query("SELECT id, task_id, client_name, signature_date, executor_name FROM info_akts ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Balti24 - AdminActs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="stylesindex.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">Balti24</div>
            <nav class="nav">
                <ul>
                    <li><a href="admintask.php">Table of orders</a></li>
                    <li><a href="admincalendar.php">Employee Calendar</a></li>
                    <li><a href="adminuser.php">Users table</a></li>
                    <li><a href="adminworker.php">Table of employees</a></li>
                    <li><a href="admincomplete.php">Completed orders</a></li>
                    <li><a href="adminacts.php">Acts</a></li>
                </ul>
            </nav>
            <p class="text-center"><a href="exit_admin_conf.php" class="btn btn-link">Log out</a></p>
        </div>
    </header>
<div class="container mt-5">
    <h2 class="mb-4">Acts of completed works</h2>
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Task ID</th>
                <th>Signature date</th>
                <th>Client</th>
                <th>Executor</th>
                <th>PDF</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['task_id']) ?></td>
                <td><?= htmlspecialchars($row['signature_date']) ?></td>
                <td><?= htmlspecialchars($row['client_name']) ?></td>
                <td><?= htmlspecialchars($row['executor_name']) ?></td>
                <td><a href="generate_pdf.php?id=<?= $row['id'] ?>" target="_blank" class="btn btn-sm btn-primary">Открыть PDF</a></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
