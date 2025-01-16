<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$login = filter_var(trim($_POST['login']), FILTER_SANITIZE_SPECIAL_CHARS);
$name = filter_var(trim($_POST['name']), FILTER_SANITIZE_SPECIAL_CHARS);
$surname = filter_var(trim($_POST['surname']), FILTER_SANITIZE_SPECIAL_CHARS);
$password = filter_var(trim($_POST['password']), FILTER_SANITIZE_SPECIAL_CHARS);

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

$mysql = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysql->connect_error) {
    die("Database connection failed: " . $mysql->connect_error);
}

$query = "INSERT INTO admins (login, name, surname, password)
          VALUES ('$login', '$name', '$surname', '$password')";
if (!$mysql->query($query)) {
    die("Query error: " . $mysql->error);
}

header('Location: adminpage.php');
exit();
?>
