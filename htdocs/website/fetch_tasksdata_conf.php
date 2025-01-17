<?php
// Подключение к базе данных
$mysql = new mysqli('localhost', 'root', '', 'balti24db');

// Проверка подключения
if ($mysql->connect_error) {
    die("Ошибка подключения: " . $mysql->connect_error);
}

// Выполнение запроса к таблице
$query = "SELECT * FROM tasks"; // Замените 'admins' на имя вашей таблицы
$result = $mysql->query($query);

// Проверяем, есть ли данные в таблице
if ($result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>ID пользователя</th><th>area</th><th>address</th><th>city</th><th>country</th><th>date</th><th>task</th><th>additional</th></tr>";

    // Вывод данных построчно
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['user_id'] . "</td>";
        echo "<td>" . $row['area'] . "</td>";
        echo "<td>" . $row['address'] . "</td>";
        echo "<td>" . $row['city'] . "</td>";
        echo "<td>" . $row['country'] . "</td>";
        echo "<td>" . $row['date'] . "</td>";
        echo "<td>" . $row['task'] . "</td>";
        echo "<td>" . $row['additional'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p style='text-align:center;'>Нет данных для отображения.</p>";
}

// Закрываем соединение
$mysql->close();
?>
