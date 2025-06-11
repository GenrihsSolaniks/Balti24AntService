<?php
// Подключение к базе данных
$mysql = new mysqli('localhost', 'root', '', 'balti24db');

// Проверка подключения
if ($mysql->connect_error) {
    die("Connection error: " . $mysql->connect_error);
}

// Выполнение запроса к таблице
$query = "SELECT * FROM users"; // Замените 'admins' на имя вашей таблицы
$result = $mysql->query($query);

// Проверяем, есть ли данные в таблице
if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>user name</th></tr>";

    // Вывод данных построчно
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p style='text-align:center;'>No data to display.</p>";
}

// Закрываем соединение
$mysql->close();
?>
