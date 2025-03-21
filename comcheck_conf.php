<?php
// Начинаем сессию
session_start();

// Получаем данные из формы и фильтруем их
$login = filter_var(trim($_POST['login']), FILTER_SANITIZE_SPECIAL_CHARS);
$password = filter_var(trim($_POST['password']), FILTER_SANITIZE_SPECIAL_CHARS);
$company = filter_var(trim($_POST['company']), FILTER_SANITIZE_SPECIAL_CHARS);
$phone = filter_var(trim($_POST['phone']), FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_SPECIAL_CHARS);

// Проверяем длину данных
if (mb_strlen($login) < 5 || mb_strlen($login) > 90) {
    die("Login must be from 5 to 90 characters");
}
if (mb_strlen($name) < 3 || mb_strlen($name) > 50) {
    die("Name must be from 3 to 50 characters");
}
if (mb_strlen($password) < 2 || mb_strlen($password) > 6) {
    die("Password must be from 2 to 6 characters");
}

// Хешируем пароль
//$password_hashed = password_hash($password, PASSWORD_DEFAULT);

// Подключаемся к базе данных
$mysql = new mysqli('localhost', 'root', '', 'balti24db');

// Проверяем подключение
if ($mysql->connect_error) {
    die("Ошибка подключения: " . $mysql->connect_error);
}

// Проверяем, существует ли уже такой логин в одной из таблиц
$stmt = $mysql->prepare("
    SELECT 'users' AS source FROM `users` WHERE `login` = ?
    UNION
    SELECT 'company' FROM `company` WHERE `login` = ?
    UNION
    SELECT 'admins' FROM `admins` WHERE `login` = ?
    UNION
    SELECT 'workers' FROM `workers` WHERE `login` = ?
");
$stmt->bind_param("ssss", $login, $login, $login, $login);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die("Ошибка: Пользователь с таким логином уже существует в одной из таблиц.");
}

// Подготовленный запрос для вставки нового пользователя
$stmt = $mysql->prepare("INSERT INTO `company` (`login`, `name`, `password`) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $login, $name, $password);

// Выполняем запрос
if (!$stmt->execute()) {
    die("Ошибка запроса: " . $stmt->error);
}

// Получаем ID нового пользователя
$user_id = $stmt->insert_id;

// Сохраняем данные пользователя в сессии
$_SESSION['user_id'] = $user_id;
$_SESSION['username'] = $name;

// Устанавливаем cookie
setcookie('user', $name, time() + 3600, "/");

// Закрываем соединение
$stmt->close();
$mysql->close();

// Перенаправляем на главную страницу
header('Location: MainSite.php');
exit();
?>