<?php
    setcookie('user', '', time() - 3600, "/"); // Удаляем cookie
    header('Location: index.php'); // Перенаправление на главную страницу
    exit();
?>
