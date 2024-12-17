<?php
    $area = filter_var(trim($_POST['ServiceArea']), FILTER_SANITIZE_SPECIAL_CHARS);
    $address = filter_var(trim($_POST['address']), FILTER_SANITIZE_SPECIAL_CHARS);
    $city = filter_var(trim($_POST['city']), FILTER_SANITIZE_SPECIAL_CHARS);
    $country = filter_var(trim($_POST['country']), FILTER_SANITIZE_SPECIAL_CHARS);
    $date = filter_var(trim($_POST['date']), FILTER_SANITIZE_SPECIAL_CHARS);
    $task = filter_var(trim($_POST['taskDescription']), FILTER_SANITIZE_SPECIAL_CHARS);
    $additional = filter_var(trim($_POST['details']), FILTER_SANITIZE_SPECIAL_CHARS);
    
    
    /*if(mb_strlen($login) < 5 || mb_strlen($login) > 90){
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
    exit(); */

    //$password = md5($password."qweqweqwe123");

    if (empty($area) || empty($address) || empty($city)) {
        die("Ошибка: все поля должны быть заполнены!");
    }
    
    $mysql = new mysqli('localhost', 'root', '', 'balti24db');
    if ($mysql->connect_error) {
        die("Ошибка подключения: " . $mysql->connect_error);
    }
    
    $query = "INSERT INTO tasks (area, address, city, country, date, task, additional) 
              VALUES ('$area', '$address', '$city', '$country', '$date', '$task', '$additional')";
    
    if (!$mysql->query($query)) {
        die("Ошибка запроса: " . $mysql->error);
    }
    
    $mysql->close();
    header('Location: Stask.html');
    ?>