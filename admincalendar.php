<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Employee employment calendar</title>
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
                <li><a href="admintask.php">Table of orders</a></li>
                <li><a href="admincalendar.php">Employee Calendar</a></li>
                <li><a href="adminuser.php">Users table</a></li>
                <li><a href="adminworker.php">Table of employees</a></li>
                <li><a href="admincomplete.php">Completed orders</a></li>
            </ul>
        </nav>
        <p class="text-center"><a href="exit_admin_conf.php" class="btn btn-link">Log out</a></p>
    </div>
</header>

<div class="filters">
    <label for="filterEmployee">Employee:</label>
    <input type="number" id="filterEmployee" placeholder="ID сотрудника">

    <label for="filterUser">User:</label>
    <input type="number" id="filterUser" placeholder="ID пользователя">

    <label for="filterDate">Date:</label>
    <input type="date" id="filterDate">

    <label for="filterType">Area of work:</label>
    <select id="filterType">
        <option value="">All</option>
        <option value="Cleaning">Cleaning</option>
        <option value="Repair">Repair</option>
    </select>

    <button id="applyFilters">Apply</button>
</div>


<div id="workerInfo" class="container mt-4"></div>
<div id="calendar"></div>


<!-- Модальное окно для добавления события -->
<div id="eventModal" style="display:none; position: fixed; top: 20%; left: 30%; width: 300px; background: #fff; border: 1px solid #ccc; padding: 20px;">
    <h3>Add an order</h3>
    <form id="eventForm">
        <label for="employeeId">employee ID:</label>
        <input type="number" id="employeeId" name="employee_id" required><br><br>
        
        <label for="orderId">order ID:</label>
        <input type="number" id="orderId" name="order_id" required><br><br>

        <label for="eventType">Area of work:</label>
        <select id="eventType" name="type" required>
            <option value="Cleaning">Cleaning</option>
            <option value="Repair">Repair</option>
        </select><br><br>

        <label for="userId">user ID:</label>
        <input type="number" id="userId" name="user_id"required><br><br>
        
        <label for="eventDate">Date:</label>
        <input type="date" id="eventDate" name="date" required><br><br>
        
        <label for="startTime">Start time:</label>
        <input type="time" id="startTime" name="start_time" required><br><br>
        
        <label for="endTime">End time :</label>
        <input type="time" id="endTime" name="end_time" required><br><br>
        
        <button type="button" id="saveEvent">Save</button>
        <button type="button" id="closeModal">Cancel</button>
    </form>
</div>

<footer class="footer">
    <div class="container">
        <p>&copy; 2025 Balti24. All rights reserved.</p>
    </div>
</footer>

<script>
var calendar; // Глобальная переменная

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    
    calendar = new FullCalendar.Calendar(calendarEl, { // Убираем var перед calendar
        initialView: 'dayGridMonth',
        events: 'get_events.php',
        displayEventTime: false,

        eventDidMount: function(info) {
            info.el.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                if (confirm("Delete this order?")) {
                    $.post('delete_event.php', { id: info.event.id }, function(response) {
                        alert(response);
                        calendar.refetchEvents();
                    });
                }
            });

            info.el.addEventListener('dblclick', function() {
                let orderMatch = info.event.title.match(/Order (\d+)/);
                let employeeMatch = info.event.title.match(/Emp\. (\d+)/);

                if (!orderMatch || !employeeMatch) {
                    alert("Error: Failed to retrieve order or employee ID!");
                    return;
                }

                let newOrderId = prompt("Enter a new order ID:", orderMatch[1]);
                let newEmployeeId = prompt("Enter the new employee ID:", employeeMatch[1]);
                let newStartTime = prompt("Enter the new start time (HH:MM):", info.event.start.toISOString().substring(11, 16));
                let newEndTime = prompt("Enter the new end time (HH:MM):", info.event.end.toISOString().substring(11, 16));
                let newUserId = prompt("Enter a new user ID:", info.event.extendedProps.user_id);

                if (newOrderId && newEmployeeId && newStartTime && newEndTime && newUserId) {
                    let data = {
                        id: info.event.id,
                        order_id: newOrderId,
                        employee_id: newEmployeeId,
                        start_time: newStartTime,
                        end_time: newEndTime,
                        user_id: newUserId
                    };

                    $.post('update_event.php', data, function(response) {
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

    $('#saveEvent').on('click', function() {
    var data = {
        employee_id: $('#employeeId').val(),
        order_id: $('#orderId').val(),
        user_id: $('#userId').val(),
        date: $('#eventDate').val(),
        start_time: $('#startTime').val(),
        end_time: $('#endTime').val(),
        type: $('#eventType').val() // Новый параметр
    };

    $.post('save_event.php', data, function(response){
        alert(response);
        $('#eventModal').hide();
        calendar.refetchEvents();
    });
    });
});

// ✅ Теперь обработчик фильтров будет работать
document.getElementById("applyFilters").addEventListener("click", function() {
    var employee_id = document.getElementById("filterEmployee").value;
    // Если выбран сотрудник, получаем его инфу
    if (employee_id) {
        fetch('get_worker_info.php?id=' + employee_id)
            .then(response => response.json())
            .then(data => {
                if (data && data.success) {
                    const info = data.worker;
                    document.getElementById("workerInfo").innerHTML = `
                        <div class="card p-3 shadow-sm">
                            <h5>Employee information #${info.id}</h5>
                            <p><strong>First Name:</strong> ${info.name}</p>
                            <p><strong>Last Name:</strong> ${info.surname}</p>
                            <p><strong>Area:</strong> ${info.type}</p>
                            <p><strong>Phone:</strong> ${info.number}</p>
                        </div>
                    `;
                } else {
                    document.getElementById("workerInfo").innerHTML = "<p>Employee not found.</p>";
                }
            });
    } else {
        document.getElementById("workerInfo").innerHTML = ""; // Очистить при пустом ID
    }

    var user_id = document.getElementById("filterUser").value;
    var date = document.getElementById("filterDate").value;
    var type = document.getElementById("filterType").value;

    var queryParams = [];
    if (employee_id) queryParams.push("employee_id=" + employee_id);
    if (user_id) queryParams.push("user_id=" + user_id);
    if (date) queryParams.push("date=" + date);
    if (type) queryParams.push("type=" + type);

    var queryString = queryParams.length ? "?" + queryParams.join("&") : "";

    console.log("The filter is applied with a query:", queryString);

    calendar.setOption('events', 'get_events.php' + queryString);
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