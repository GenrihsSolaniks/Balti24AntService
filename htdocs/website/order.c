<?php
session_start(); // Начинаем сессию

if (!isset($_SESSION['user_id'])) {
    die("Ошибка: пользователь не авторизован!");
}

// Получаем данные из формы
$area = filter_var(trim($_POST['ServiceArea'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);
$address = filter_var(trim($_POST['address'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);
$city = filter_var(trim($_POST['city'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);
$country = filter_var(trim($_POST['country'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);
$date = filter_var(trim($_POST['date'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);
$task = filter_var(trim($_POST['taskDescription'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);
$additional = filter_var(trim($_POST['details'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);

// Проверка обязательных полей
if (empty($area) || empty($address) || empty($city)) {
    die("Ошибка: все поля должны быть заполнены!");
}

// Подключаемся к базе данных
$mysql = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysql->connect_error) {
    die("Ошибка подключения к базе данных: " . $mysql->connect_error);
}

// Получаем логин пользователя из сессии
$user_id = $_SESSION['user_id'];
$result = $mysql->query("SELECT login FROM users WHERE id='$user_id'");

if ($result && $row = $result->fetch_assoc()) {
    $login = $row['login'];
} else {
    die("Ошибка: не удалось получить логин пользователя!");
}

// Вставляем данные в таблицу tasks
$query = "INSERT INTO tasks (login, area, address, city, country, date, task, additional)
          VALUES ('$login', '$area', '$address', '$city', '$country', '$date', '$task', '$additional')";

if (!$mysql->query($query)) {
    die("Ошибка запроса: " . $mysql->error);
}

// Закрываем соединение с базой данных и перенаправляем на страницу заказов
$mysql->close();
header('Location: STask.html');
exit();
?>
