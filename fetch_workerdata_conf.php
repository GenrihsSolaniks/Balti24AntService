<?php
// Подключение к базе данных
$mysql = new mysqli('localhost', 'root', '', 'balti24db');

// Проверка подключения
if ($mysql->connect_error) {
    die("Connection error: " . $mysql->connect_error);
}

// Выполнение запроса к таблице
$query = "SELECT * FROM workers"; // Замените 'workers' на имя вашей таблицы
$result = $mysql->query($query);

// Проверяем, есть ли данные в таблице
if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>ID</th><th>Name</th><th>Surname</th><th>Type</th><th>PhoneNumber</th></tr>";

    // Вывод данных построчно
    while ($row = $result->fetch_assoc()) {
        $phoneNumber = preg_replace('/[^0-9]/', '', $row['number']); // Очищаем номер от лишних символов
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['surname'] . "</td>";
        echo "<td>" . $row['type'] . "</td>";
        echo "<td><a href='https://wa.me/$phoneNumber' target='_blank'>" . $row['number'] . "</a></td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p style='text-align:center;'>No data to display.</p>";
}

// Закрываем соединение
$mysql->close();
?>
