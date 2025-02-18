<?php
    setcookie('admin', '', time() - 3600, "/"); // Удаляем cookie
    header('Location: adminauth.html'); // Перенаправление на главную страницу
    exit();
?>
