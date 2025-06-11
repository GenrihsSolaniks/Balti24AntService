<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Берём ID пользователя из сессии

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>My orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="stylesindex.css">
</head>
<body>
<header class="header">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="logo">Balti24</div>
            <nav class="nav">
                <ul class="d-flex list-unstyled mb-0">
                    <li><a href="index.html">Home</a></li>
                    <li><a href="about.html">About</a></li>
                    <li><a href="my_orders.php">Orders</a></li>
                    <li class="me-3"><a href="user_order.php">The time of my orders</a></li>
                    <li><a href="MainSite.php">Fill out the order form</a></li>
                    <li><a href="contact.html">Contacts</a></li>
                </ul>
            </nav>
            <!-- Блок для кнопок -->
            <div class="auth-buttons">
                <a href="index.php" class="btn btn-outline-primary me-2">Log in</a>
                <a href="index.php" class="btn btn-primary">Sign up</a>
            </div>
        </div>
    </header>
    <div class="container mt-4">
        <h2>Your orders</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Start time</th>
                    <th>End time</th>
                    <th>Responsible employee</th>
                </tr>
            </thead>
            <tbody id="ordersTable">
                <!-- Данные подгрузятся сюда -->
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function(){
            $.post('get_user_orders.php', {}, function(response){
                let orders = JSON.parse(response);
                let tableBody = "";
                orders.forEach(order => {
                    tableBody += `<tr>
                        <td>${order.order_id}</td>
                        <td>${order.date}</td>
                        <td>${order.start_time}</td>
                        <td>${order.end_time}</td>
                        <td>${order.employee_id ? 'Сотрудник ' + order.employee_id : 'Не назначен'}</td>
                    </tr>`;
                });
                $("#ordersTable").html(tableBody);
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
