<?php
     $login = filter_var(trim($_POST['login']), FILTER_SANITIZE_SPECIAL_CHARS);
     $password = filter_var(trim($_POST['password']), FILTER_SANITIZE_SPECIAL_CHARS);


    //$password = md5($password."qweqweqwe123");

    if ($login === 'login' && $password === 'pass') {
        setcookie('user', $login, time() + 3600, "/"); // Устанавливаем cookie на час
        header('Location: MainSite.php'); // Перенаправление на MainSite.php
        exit(); // Завершаем выполнение скрипта
    } else {
        echo "Invalid login or password<br>";
    }

    $mysql = new mysqli('localhost', 'root', '', 'balti24db');

    $result = $mysql->query("SELECT * FROM `users` WHERE `login` = '$login' AND `pass` = '$password'");

    $user = $result->fetch_assoc();

    if($user === null){
        echo " User not found";
        exit();
    }

    setcookie('user', $user['name'], time() + 3600, "/");

    

    $mysql->close();

    header('Location: MainSite.php');
?>