<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Ошибка: Необходима авторизация.");
}

$user_id = $_SESSION['user_id']; // Берём ID пользователя из сессии

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мои заказы</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h2>Ваши заказы</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID заказа</th>
                    <th>Дата</th>
                    <th>Время начала</th>
                    <th>Время окончания</th>
                    <th>Ответственный сотрудник</th>
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
</body>
</html>
