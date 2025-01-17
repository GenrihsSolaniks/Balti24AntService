<?php
// Стартуем сессию
session_start();

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    die("Ошибка: пользователь не авторизован.");
}

// Получаем ID пользователя из сессии
$user_id = $_SESSION['user_id'];

// Фильтруем данные из формы
$area = filter_var(trim($_POST['ServiceArea']), FILTER_SANITIZE_SPECIAL_CHARS);
$address = filter_var(trim($_POST['address']), FILTER_SANITIZE_SPECIAL_CHARS);
$city = filter_var(trim($_POST['city']), FILTER_SANITIZE_SPECIAL_CHARS);
$country = filter_var(trim($_POST['country']), FILTER_SANITIZE_SPECIAL_CHARS);
$date = filter_var(trim($_POST['date']), FILTER_SANITIZE_SPECIAL_CHARS);
$task = filter_var(trim($_POST['taskDescription']), FILTER_SANITIZE_SPECIAL_CHARS);
$additional = filter_var(trim($_POST['details']), FILTER_SANITIZE_SPECIAL_CHARS);

// Проверяем, заполнены ли обязательные поля
if (empty($area) || empty($address) || empty($city) || empty($country) || empty($date) || empty($task)) {
    die("Ошибка: все обязательные поля должны быть заполнены!");
}

// Подключаемся к базе данных
$mysql = new mysqli('localhost', 'root', '', 'balti24db');

// Проверяем подключение
if ($mysql->connect_error) {
    die("Ошибка подключения: " . $mysql->connect_error);
}

// Подготовленный запрос для вставки данных
$stmt = $mysql->prepare("INSERT INTO tasks (user_id, area, address, city, country, date, task, additional) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

// Связываем параметры
$stmt->bind_param("isssssss", $user_id, $area, $address, $city, $country, $date, $task, $additional);

// Выполняем запрос
if (!$stmt->execute()) {
    die("Ошибка запроса: " . $stmt->error);
}

// Закрываем соединение
$stmt->close();
$mysql->close();

// Перенаправляем на страницу успешного создания задачи
header('Location: Stask.html');
exit();
?>
