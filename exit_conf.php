<?php
session_start();
session_unset(); // Удаляет все переменные сессии
session_destroy(); // Уничтожает сессию

// Удалим куку с именем
setcookie('user', '', time() - 3600, '/');

header("Location: index.php");
exit();
?>