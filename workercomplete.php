<?php
if (!isset($_COOKIE['worker_id']) || empty($_COOKIE['worker_id'])) {
    die("Ошибка: worker_id не установлен");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Balti24 - Главная</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="stylesindex.css">
</head>
<body>
<header class="header">
    <div class="container">
        <div class="logo">Balti24</div>
        <nav class="nav">
            <ul>
                <li><a href="worker.php">Order table</a></li>
                <li><a href="worker_schedule.php">My employment</a></li>
                <li><a href="workercomplete.php">Completed orders</a></li>
                <li><a href="choose_template.php">Generate an act</a></li>
            </ul>
        </nav>
        <p class="text-center"><a href="exit_worker_conf.php" class="btn btn-link">Log out</a></p>
    </div>
</header>
<h1 style="text-align:center;">Real-Time MySQL Data</h1>
<div id="tasks-container" style="text-align: center;">
    <!-- Здесь появятся задачи -->
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    loadTasks();
});

// Функция загрузки задач для работника
function loadTasks() {
    fetch('workercomp_conf.php')
        .then(response => response.text())
        .then(data => {
            let container = document.getElementById('tasks-container');
            if (container) {
                container.innerHTML = data;
            } else {
                console.error("Ошибка: не найден элемент с ID 'tasks-container'.");
            }
        })
        .catch(error => console.error('Ошибка загрузки задач:', error));
}

// Функция принятия заказа
function acceptOrder(orderId) {
    fetch('update_order_status_conf.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: orderId, action: 'acceptOrder' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Заказ принят!');
            loadTasks(); // Перезагрузка списка задач
        } else {
            alert('Ошибка: ' + data.message);
        }
    })
    .catch(error => console.error('Ошибка при принятии заказа:', error));
}

// Функция завершения заказа
function completeOrder(orderId) {
    fetch('update_order_status_conf.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: orderId, action: 'completeOrder' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Заказ завершён!');
            loadTasks(); // Перезагрузка списка задач
        } else {
            alert('Ошибка: ' + data.message);
        }
    })
    .catch(error => console.error('Ошибка при завершении заказа:', error));
}

// Обновление данных каждые 5 секунд
setInterval(loadTasks, 5000);
</script>
<footer class="footer">
    <div class="container">
         <p>&copy; 2025 Balti24. All rights reserved.</p>
    </div>
</footer>
</body>
</html>
<style>
table {
    width: 80%;
    margin: 20px auto;
    border-collapse: collapse;
    font-family: Arial, sans-serif;
    font-size: 14px;
    text-align: left;
}

table th, table td {
    border: 1px solid #ddd;
    padding: 10px;
}

table th {
    background-color: #f4f4f4;
    font-weight: bold;
    text-align: center;
}

table tr:nth-child(even) {
    background-color: #f9f9f9;
}

table tr:hover {
    background-color: #f1f1f1;
}

.highlight-yellow {
    background-color: yellow !important;
}

.highlight-green {
    background-color: lightgreen !important;
}
</style>
