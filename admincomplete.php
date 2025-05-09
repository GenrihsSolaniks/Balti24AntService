<?php
if (!isset($_COOKIE['admin_id'])) {
    header('Location: adminauth.html'); // Перенаправление на adminauth.html
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Balti24 - AdminComplete</title>
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
    <h1 style="text-align:center;">Table of completed orders</h1>
<div id="data-container">
    <!-- Таблица будет загружаться сюда -->
</div>
<button onclick="window.location.href='export_conf.php'">Download csv table</button>

<script>
    function updateOrderStatus(orderId, action) {
        fetch('update_order_status_conf.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: orderId, action: action })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Status updated successfully!');
                location.reload(); // Обновляем страницу
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
    function loadData() {
    // Сохраняем текущие выбранные worker_id перед обновлением
    let selectedWorkers = {};
    document.querySelectorAll("select[id^='worker-select-']").forEach(select => {
        selectedWorkers[select.id] = select.value;
    });

    // Загружаем новые данные
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "admincom_conf.php", true);
    xhr.onload = function () {
        if (this.status === 200) {
            document.getElementById('data-container').innerHTML = this.responseText;

            // Восстанавливаем выбор работников после обновления данных
            document.querySelectorAll("select[id^='worker-select-']").forEach(select => {
                if (selectedWorkers[select.id]) {
                    select.value = selectedWorkers[select.id];
                }
            });
        }
    };
    xhr.send();
}
    function assignWorker(orderId) {
    const workerId = document.getElementById(`worker-select-${orderId}`).value;

    fetch('assign_worker_conf.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ order_id: orderId, worker_id: workerId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Employee successfully assigned to an order!');
            location.reload(); // Перезагрузка страницы
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

    // Загружаем данные при загрузке страницы
    loadData();

    // Обновляем данные каждые 5 секунд
    setInterval(loadData, 5000);
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
