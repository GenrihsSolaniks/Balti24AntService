<?php
if (!isset($_COOKIE['admin_id'])) {
    header('Location: adminauth.html'); // Перенаправление на adminauth.html
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Balti24 - Users table</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="stylesindex.css">
    <style>
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
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
    <h1 style="text-align:center;">User table</h1>
    <div id="data-container">
        <!-- Таблица будет загружаться сюда -->
    </div>

    <script>
        // Функция для загрузки данных
        function loadData() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "fetch_userdata_conf.php", true);
            xhr.onload = function() {
                if (this.status === 200) {
                    document.getElementById('data-container').innerHTML = this.responseText;
                }
            }
            xhr.send();
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
