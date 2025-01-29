<?php
    setcookie('worker', '', time() - 3600, "/"); // Удаляем cookie
    header('Location: workeraunth.html'); // Перенаправление на workeraunth.html
    exit();
?>
