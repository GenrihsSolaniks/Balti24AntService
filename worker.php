<?php
if (!isset($_COOKIE['worker_id']) || empty($_COOKIE['worker_id'])) {
    die("Ошибка: worker_id не установлен");
}
?>
<!DOCTYPE html>
<html lang="ru">
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
                <li><a href="worker.php">Таблица заказов</a></li>
                <li><a href="worker_schedule.php">Моя занятость</a></li>
                <li><a href="workercomplete.php">Выполненные заказы</a></li>
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

// Универсальная функция обновления статуса заказа
function updateOrderStatus(orderId, action) {
    fetch('update_order_status_conf.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: orderId, action: action })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadTasks();  // Обновляем список задач
        } else {
            alert('Ошибка: ' + data.message);
        }
    })
    .catch(error => console.error('Ошибка:', error));
}

// Функция загрузки задач для работника
function loadTasks() {
    fetch('workertask_conf.php')
        .then(response => response.text())
        .then(data => {
            const container = document.getElementById('tasks-container');
            if (container) {
                container.innerHTML = data;

                // После отрисовки в DOM — подключаем обработчики
                setTimeout(() => {
                    // 1. Включаем нужные кнопки из localStorage
                    document.querySelectorAll('[id^="completeBtn-"]').forEach(button => {
                        const orderId = button.id.split('-')[1];
                        if (localStorage.getItem('act_shown_' + orderId) === '1') {
                            button.disabled = false;
                        }
                    });

                    // 2. Назначаем обработчики кнопок вручную
                    document.querySelectorAll('[id^="openActBtn-"]').forEach(btn => {
                        const id = btn.id.split('-')[1];
                        btn.onclick = () => openWorkAct(id);
                    });

                    document.querySelectorAll('[id^="updateBtn-"]').forEach(btn => {
                        const id = btn.id.split('-')[1];
                        const action = btn.getAttribute("data-action");
                        btn.onclick = () => updateOrderStatus(id, action);
                    });

                    document.querySelectorAll('[id^="pauseBtn-"]').forEach(btn => {
                        const id = btn.id.split('-')[1];
                        btn.onclick = () => togglePauseStatus(id);
                    });
                }, 100);
            }
        })
        .catch(error => console.error('Ошибка загрузки задач:', error));
}


// Функция принятия заказа
function acceptOrder(orderId) {
    updateOrderStatus(orderId, 'acceptOrder');
}

// Функция завершения заказа
function completeOrder(orderId) {
    updateOrderStatus(orderId, 'completeOrder');
}

// Функция переключения паузы
function togglePauseStatus(orderId) {
    console.log("Отправка запроса togglePause для заказа ID:", orderId);

    fetch('update_order_status_conf.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: orderId, action: 'togglePause' })
    })
    .then(response => response.json())
    .then(data => {
        console.log("Ответ от сервера:", data);
        if (data.success) {
            location.reload();
        } else {
            alert("Ошибка от сервера: " + data.message);
        }
    })
    .catch(error => console.error("Ошибка запроса:", error));
}

function openWorkAct(orderId) {
    const win = window.open(`http://127.0.0.1:5000/form?task_id=${orderId}`, '_blank');
    const check = setInterval(() => {
        if (win.closed) {
            clearInterval(check);
            const btn = document.getElementById('completeBtn-' + orderId);
            if (btn) btn.disabled = false;
        }
    }, 500);
}




// Обновление данных каждые 5 секунд
setInterval(loadTasks, 5000);
</script>

<footer class="footer">
    <div class="container">
        <p>&copy; 2025 Balti24. Все права защищены.</p>
    </div>
</footer>

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

</body>
</html>
