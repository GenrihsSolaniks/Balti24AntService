<?php
if (!isset($_COOKIE['user'])) {
    header('Location: workeraunth.html'); // Перенаправление на workeraunth.html
    exit();
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
        <p class="text-center"><a href="exit_worker_conf.php" class="btn btn-link">Log out</a></p>
    </div>
</header>
<h1 style="text-align:center;">Real-Time MySQL Data</h1>
<div id="data-container">
    <!-- Таблица будет загружаться сюда -->
</div>

<script>
    function acceptOrder(orderId) {
    fetch('update_order_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: orderId, action: 'acceptOrder' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Заказ принят!');
            location.reload(); // Обновляем страницу
        } else {
            alert('Ошибка: ' + data.message);
        }
    })
    .catch(error => console.error('Ошибка:', error));
}

    function loadData() {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", "workertask.php", true);
        xhr.onload = function () {
            if (this.status === 200) {
                document.getElementById('data-container').innerHTML = this.responseText;
            }
        };
        xhr.send();
    }

    // Загружаем данные при загрузке страницы
    loadData();

    // Обновляем данные каждые 5 секунд
    setInterval(loadData, 5000);
</script>
<footer class="footer">
    <div class="container">
        <p>&copy; 2025 Balti24. Все права защищены.</p>
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
