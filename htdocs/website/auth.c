<?php
session_start();

// Подключение к базе данных
$mysql = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysql->connect_error) {
    die("Ошибка подключения: " . $mysql->connect_error);
}

// Получаем данные из формы
$login = filter_var(trim($_POST['login']), FILTER_SANITIZE_SPECIAL_CHARS);
$password = md5(filter_var(trim($_POST['password']), FILTER_SANITIZE_SPECIAL_CHARS) . "qweqweqwe123");

// Проверяем пользователя
$result = $mysql->query("SELECT id, login FROM users WHERE login='$login' AND pass='$password'");
$user = $result->fetch_assoc();

if ($user) {
    // Сохраняем данные пользователя в сессии
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_login'] = $user['login'];

    // Перенаправляем на страницу заказов
    header('Location: order.php');
} else {
    die("Ошибка: неверный логин или пароль!");
}

$mysql->close();
?>
