<?php
// Настройки подключения к базе данных
$host = "localhost";
$username = "root";
$password = "";
$database = "balti24db";

// Подключение к базе данных
$conn = new mysqli($host, $username, $password, $database);

// Проверка подключения
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Запрос к таблице
$table = "admins"; // Укажите имя вашей таблицы
$query = "SELECT * FROM $table";
$result = $conn->query($query);

// Установка заголовков для скачивания CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=data.csv');

// Открытие файла в памяти
$output = fopen('php://output', 'w');

// Добавление заголовков столбцов
if ($result->num_rows > 0) {
    $columns = $result->fetch_fields();
    $headers = [];
    foreach ($columns as $column) {
        $headers[] = $column->name;
    }
    fputcsv($output, $headers);

    // Добавление строк данных
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

// Закрытие соединения
fclose($output);
$conn->close();
?>
