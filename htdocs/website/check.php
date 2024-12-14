<?php
    $login = filter_var(trim($_POST['login']), FILTER_SANITIZE_SPECIAL_CHARS);
    $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_var(trim($_POST['password']), FILTER_SANITIZE_SPECIAL_CHARS);
    
    if(mb_strlen($login) < 5 || mb_strlen($login) > 90){
        echo"Login must be from 5 to 90 characters";
        exit();
    }else if(mb_strlen($name) < 3 || mb_strlen($name) > 50){
        echo"Name must be from 3 to 50 characters";
        exit();
    }else if(mb_strlen($password) < 2 || mb_strlen($password) > 6){
        echo"Password must be from 2 to 6 characters";
        exit();
    }

    setcookie('user', $login, time() + 3600, "/"); // Устанавливаем cookie на час
    header('Location: MainSite.php'); // Перенаправление на MainSite.html
    exit(); 

    //$password = md5($password."qweqweqwe123");

    $mysql = new mysqli('localhost', 'root', '', 'balti24db');
    $mysql->query("INSERT INTO `users` (`login`,`name`,`pass`) VALUES('$login', '$name', '$password')");
    $mysql->close();

    header('Location: MainSite.php');
?>