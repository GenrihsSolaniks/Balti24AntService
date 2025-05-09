<?php
// Настройки подключения
$host = "localhost";
$username = "root";
$password = "";
$database = "balti24db";

// Подключение
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Выбор таблицы
$table = $conn->real_escape_string("completetask");

// Запрос
$query = "SELECT * FROM $table";
$result = $conn->query($query);

// Проверка наличия данных
if ($result->num_rows == 0) {
    echo "Нет данных для экспорта";
    exit();
}

// Заголовки CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=completetask.csv');

// Открытие файла в памяти
$output = fopen('php://output', 'w');
echo "\xEF\xBB\xBF"; // Добавляет BOM для корректного отображения в Excel

// Запись заголовков
$columns = $result->fetch_fields();
$headers = [];
foreach ($columns as $column) {
    $headers[] = $column->name;
}
fputcsv($output, $headers);

// Запись данных
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

// Закрытие соединения
fclose($output);
$conn->close();
?>
