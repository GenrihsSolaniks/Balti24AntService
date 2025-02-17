<?php
$servername = "localhost";
$username = "root"; // Замени на своего пользователя БД
$password = ""; // Если есть пароль - укажи
$database = "balti24db";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

// Получаем ID события
$event_id = $_POST['id'] ?? null;

if ($event_id) {
    $sql = "DELETE FROM employee_schedule WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $event_id);
    
    if ($stmt->execute()) {
        echo "Событие удалено!";
    } else {
        echo "Ошибка при удалении!";
    }
} else {
    echo "Ошибка: ID события не найден!";
}

$conn->close();
?>
