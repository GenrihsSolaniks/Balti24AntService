<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Календарь занятости сотрудников</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="stylesindex.css">

  <!-- Подключаем стили FullCalendar (CDN) -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
  
  <!-- Подключаем библиотеки -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
</head>
<body>
<?php
// Проверка куки
if (!isset($_COOKIE['admin_id'])) {
    header('Location: adminauth.html'); 
    exit();
}
?>
<header class="header">
    <div class="container">
        <div class="logo">Balti24</div>
        <nav class="nav">
            <ul>
                <li><a href="admintask.php">Таблица заказов</a></li>
                <li><a href="admincalendar.php">Календарь работников</a></li>
                <li><a href="adminuser.php">Таблица пользователей</a></li>
                <li><a href="adminworker.php">Таблица работников</a></li>
                <li><a href="admincomplete.php">Выполненые заказы</a></li>
            </ul>
        </nav>
        <p class="text-center"><a href="exit_admin_conf.php" class="btn btn-link">Log out</a></p>
    </div>
</header>

<div id="calendar"></div>

<!-- Модальное окно для добавления события -->
<div id="eventModal" style="display:none; position: fixed; top: 20%; left: 30%; width: 300px; background: #fff; border: 1px solid #ccc; padding: 20px;">
    <h3>Добавить событие</h3>
    <form id="eventForm">
        <label for="employeeId">ID сотрудника:</label>
        <input type="number" id="employeeId" name="employee_id" required><br><br>
        
        <label for="orderId">ID заказа:</label>
        <input type="number" id="orderId" name="order_id" required><br><br>
        
        <label for="eventDate">Дата:</label>
        <input type="date" id="eventDate" name="date" required><br><br>
        
        <label for="startTime">Время начала:</label>
        <input type="time" id="startTime" name="start_time" required><br><br>
        
        <label for="endTime">Время окончания:</label>
        <input type="time" id="endTime" name="end_time" required><br><br>
        
        <button type="button" id="saveEvent">Сохранить</button>
        <button type="button" id="closeModal">Отмена</button>
    </form>
</div>

<footer class="footer">
    <div class="container">
        <p>&copy; 2025 Balti24. Все права защищены.</p>
    </div>
</footer>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: 'get_events.php',
        displayEventTime: false,

        eventDidMount: function(info) {
            // Удаление по ПКМ (правый клик)
            info.el.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                if (confirm("Удалить это событие?")) {
                    $.post('delete_event.php', { id: info.event.id }, function(response) {
                        console.log("Удаление ответа:", response);
                        alert(response);
                        calendar.refetchEvents();
                    });
                }
            });

            // Редактирование по двойному клику
            info.el.addEventListener('dblclick', function() {
    console.log("Данные события:", info.event.title); // Логируем название события

    let orderMatch = info.event.title.match(/Заказ (\d+)/);
    let employeeMatch = info.event.title.match(/Сотрудник (\d+)/); // ✅ Исправленный шаблон

    console.log("Результат orderMatch:", orderMatch);
    console.log("Результат employeeMatch:", employeeMatch);

    if (!orderMatch || !employeeMatch) {
        alert("Ошибка: не удалось получить ID заказа или сотрудника!");
        return;
    }

    let newOrderId = prompt("Введите новый ID заказа:", orderMatch[1]);
    let newEmployeeId = prompt("Введите новый ID сотрудника:", employeeMatch[1]);
    let newStartTime = prompt("Введите новое время начала (формат HH:MM):", info.event.start.toISOString().substring(11, 16));
    let newEndTime = prompt("Введите новое время окончания (формат HH:MM):", info.event.end.toISOString().substring(11, 16));

    if (newOrderId && newEmployeeId && newStartTime && newEndTime) {
        let data = {
            id: info.event.id,
            order_id: newOrderId,
            employee_id: newEmployeeId,
            start_time: newStartTime,
            end_time: newEndTime
        };

        console.log("Отправляем данные в update_event.php:", data);

        $.post('update_event.php', data, function(response) {
            console.log("Ответ сервера:", response);
            alert(response);
            calendar.refetchEvents();
        });
                }
            });
        },

        dateClick: function(info) {
            $('#eventDate').val(info.dateStr);
            $('#eventModal').show();
        }
    });

    calendar.render();

    $('#closeModal').on('click', function(){
        $('#eventModal').hide();
    });

    $('#saveEvent').on('click', function(){
        var data = {
            employee_id: $('#employeeId').val(),
            order_id: $('#orderId').val(),
            date: $('#eventDate').val(),
            start_time: $('#startTime').val(),
            end_time: $('#endTime').val()
        };

        console.log("Отправляем данные в save_event.php:", data);

        $.post('save_event.php', data, function(response){
            console.log("Ответ сервера:", response);
            alert(response);
            $('#eventModal').hide();
            calendar.refetchEvents();
        });
    });
});
</script>

</body>
</html>
<style>
#eventModal {
    display: none;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border: 1px solid #ccc;
    padding: 20px;
    box-shadow: 2px 2px 10px rgba(0,0,0,0.3);
    z-index: 1000;
    width: 400px; /* Увеличиваем ширину */
    max-height: 500px; /* Ограничиваем высоту */
    overflow-y: auto; /* Включаем прокрутку, если элементов слишком много */
}

#eventModal input,
#eventModal button {
    display: block;
    width: 100%;
    margin-top: 10px;
}

/* Затемняющий фон при открытии */
#modalBackdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
}
</style>