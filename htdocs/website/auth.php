<?php
     $login = filter_var(trim($_POST['login']), FILTER_SANITIZE_SPECIAL_CHARS);
     $password = filter_var(trim($_POST['password']), FILTER_SANITIZE_SPECIAL_CHARS);


    //$password = md5($password."qweqweqwe123");

    $mysql = new mysqli('localhost', 'root', '', 'balti24db');

    $result = $mysql->query("SELECT * FROM `users` WHERE `login` = '$login' AND `pass` = '$password'");

    $user = $result->fetch_assoc();

    if($user === null){
        echo "User not found";
        exit();
    }

    setcookie('user', $user['name'], time() + 3600, "/");

    

    $mysql->close();

    header('Location: /');
?>