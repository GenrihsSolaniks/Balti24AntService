<?php
// Начинаем сессию
session_start();

// Получаем данные из формы и фильтруем их
$login = filter_var(trim($_POST['login']), FILTER_SANITIZE_SPECIAL_CHARS);
$name = filter_var(trim($_POST['name']), FILTER_SANITIZE_SPECIAL_CHARS);
$surname = filter_var(trim($_POST['surname']), FILTER_SANITIZE_SPECIAL_CHARS);
$password = filter_var(trim($_POST['password']), FILTER_SANITIZE_SPECIAL_CHARS);
$checkword = filter_var(trim($_POST['checkword']), FILTER_SANITIZE_SPECIAL_CHARS);

if (mb_strlen($login) < 5 || mb_strlen($login) > 90) {
    die("Login must be from 5 to 90 characters");
}
if (mb_strlen($name) < 3 || mb_strlen($name) > 50) {
    die("Name must be from 3 to 50 characters");
}
if (mb_strlen($surname) < 3 || mb_strlen($surname) > 50) {
    die("Surname must be from 3 to 50 characters");
}
if (mb_strlen($password) < 2 || mb_strlen($password) > 6) {
    die("Password must be from 2 to 6 characters");
}
if ($checkword != 'admin') {
    die("Checkword is incorrect");
}

// Хешируем пароль
//$password_hashed = password_hash($password, PASSWORD_DEFAULT);

// Подключаемся к базе данных
$mysql = new mysqli('localhost', 'root', '', 'balti24db');

// Проверяем подключение
if ($mysql->connect_error) {
    die("Ошибка подключения: " . $mysql->connect_error);
}

// Проверяем, существует ли уже такой логин
$stmt = $mysql->prepare("SELECT * FROM `admins` WHERE `login` = ?");
$stmt->bind_param("s", $login);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die("Ошибка: Пользователь с таким логином уже существует.");
}

// Подготовленный запрос для вставки нового пользователя
$stmt = $mysql->prepare("INSERT INTO `admins` (`login`, `name`, `surname`,`password`) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $login, $name, $surname, $password);

// Выполняем запрос
if (!$stmt->execute()) {
    die("Ошибка запроса: " . $stmt->error);
}

// Получаем ID нового пользователя
$admin_id = $stmt->insert_id;

// Сохраняем данные пользователя в сессии
$_SESSION['admin_id'] = $admin_id; // ID администратора
$_SESSION['admin_name'] = $name; // Имя администратора

// Устанавливаем cookie
setcookie('admin_id', $admin_id, time() + 3600, "/");

// Закрываем соединение
$stmt->close();
$mysql->close();

// Перенаправляем на главную страницу
header('Location: admin.php');
exit();
?>
