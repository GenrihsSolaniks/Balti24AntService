<?php
    setcookie('user', '', time() - 3600, "/"); // Удаляем cookie
    header('Location: adminauth.html'); // Перенаправление на главную страницу
    exit();
?>
