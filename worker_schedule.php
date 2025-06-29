<?php
session_start();
if (!isset($_SESSION['worker_id'])) {
    die("Ошибка: Необходима авторизация.");
}

$worker_id = $_SESSION['worker_id']; // Берём ID работника из сессии

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Моя занятость</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    <div class="container mt-4">
        <h2>Your Planned Orders</h2>
        <table class=“table table-bordered”>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Start time</th>
                    <th>End time</th>
                </tr>
            </thead>
            <tbody id="scheduleTable">
                <!-- Данные подгрузятся сюда -->
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function(){
            $.post('get_worker_events.php', {}, function(response){
                let events = JSON.parse(response);
                let tableBody = "";
                events.forEach(event => {
                    tableBody += `<tr>
                        <td>${event.order_id}</td>
                        <td>${event.date}</td>
                        <td>${event.start_time}</td>
                        <td>${event.end_time}</td>
                    </tr>`;
                });
                $("#scheduleTable").html(tableBody);
            });
        });
    </script>

<footer class="footer">
    <div class="container">
        <p>&copy; 2025 Balti24. All rights reserved.</p>
    </div>
</footer>
</body>
</html>
