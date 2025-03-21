<?php
// Начинаем сессию
session_start();

// Получаем данные из формы и фильтруем их
$login = filter_var(trim($_POST['login']), FILTER_SANITIZE_SPECIAL_CHARS);
$name = filter_var(trim($_POST['name']), FILTER_SANITIZE_SPECIAL_CHARS);
$password = filter_var(trim($_POST['password']), FILTER_SANITIZE_SPECIAL_CHARS);

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

// Проверяем, существует ли уже такой логин
$stmt = $mysql->prepare("SELECT * FROM `users` WHERE `login` = ?");
$stmt->bind_param("s", $login);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die("Error: A user with this login already exists.");
}

// Подготовленный запрос для вставки нового пользователя
$stmt = $mysql->prepare("INSERT INTO `users` (`login`, `name`, `password`) VALUES (?, ?, ?)");
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
