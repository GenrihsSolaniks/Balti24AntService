<?php
$mysql = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysql->connect_error) {
    die("Ошибка подключения: " . $mysql->connect_error);
}

$query = "SELECT * FROM tasks WHERE status = 1"; // Только опубликованные заказы
$result = $mysql->query($query);

echo "<table>";
echo "<tr><th>ID</th><th>User ID</th><th>Area</th><th>Address</th><th>City</th><th>Country</th><th>Date</th><th>Task</th><th>Additional</th><th>Action</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['user_id']}</td>";
    echo "<td>{$row['area']}</td>";
    echo "<td>{$row['address']}</td>";
    echo "<td>{$row['city']}</td>";
    echo "<td>{$row['country']}</td>";
    echo "<td>{$row['date']}</td>";
    echo "<td>{$row['task']}</td>";
    echo "<td>{$row['additional']}</td>";
    echo "<td><button onclick=\"acceptOrder({$row['id']})\">Принять заказ</button></td>";
    echo "</tr>";
}
echo "</table>";

$mysql->close();
?>


<script>
function acceptOrder(orderId) {
    fetch('update_order_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: orderId, action: 'acceptOrder' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Заказ успешно принят!');
            location.reload(); // Обновляем страницу
        } else {
            alert('Ошибка: ' + data.message);
        }
    })
    .catch(error => console.error('Ошибка:', error));
}
</script>
