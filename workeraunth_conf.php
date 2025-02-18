<?php
// Начинаем сессию
session_start();

// Получаем данные из формы и фильтруем их
$login = filter_var(trim($_POST['login']), FILTER_SANITIZE_SPECIAL_CHARS);
$password = filter_var(trim($_POST['password']), FILTER_SANITIZE_SPECIAL_CHARS);

// Хешируем пароль, если требуется
//$password = md5($password."qweqweqwe123");

// Проверяем, указаны ли данные
if (empty($login) || empty($password)) {
    echo "Login and password are required!";
    exit();
}

// Подключаемся к базе данных
$mysql = new mysqli('localhost', 'root', '', 'balti24db');

// Проверяем подключение
if ($mysql->connect_error) {
    die("Ошибка подключения: " . $mysql->connect_error);
}

// Используем подготовленный запрос для защиты от SQL-инъекций
$stmt = $mysql->prepare("SELECT * FROM `workers` WHERE `login` = ? AND `password` = ?");
$stmt->bind_param("ss", $login, $password);
$stmt->execute();
$result = $stmt->get_result();

// Получаем данные пользователя
$worker = $result->fetch_assoc();

if ($worker === null) {
    echo "Invalid login or password";
    $stmt->close();
    $mysql->close();
    exit();
}

// Сохраняем worker_id в сессию
$_SESSION['worker_id'] = $worker['id'];

// Устанавливаем cookie с именем пользователя
setcookie('worker_id', $worker['id'], time() + 3600, "/");

// Закрываем соединение с базой
$stmt->close();
$mysql->close();

// Перенаправляем на главную страницу
header('Location: worker.php');
exit();
?>