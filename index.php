<?php
if (isset($_COOKIE['user'])) {
    // Если пользователь авторизован, покажем ему выбор
    echo "<p>Hello, " . htmlspecialchars($_COOKIE['user']) . "!</p>";
    echo "<p>You are already logged in. Go to <a href='MainSite.php'>Main Site</a> or <a href='exit.php'>Log out</a>.</p>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balti24 - Sign up</title>
    <link rel="stylesheet" href="stylelegacy.css">
</head>
<body>
<header class="header">
    <div class="container">
        <div class="logo">Balti24</div>
        <nav class="nav">
            <ul>
                <li><a href="index.html">Home</a></li>
                    <li><a href="about.html">About</a></li>
                    <li><a href="my_orders.php">Orders</a></li>
                    <li class="me-3"><a href="user_order.php">The time of my orders</a></li>
                    <li><a href="MainSite.php">Fill out the order form</a></li>
                    <li><a href="contact.html">Contacts</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="container" style="margin-top: 60px; display: flex; justify-content: center;">
    <form action="auth_conf.php" method="post">
        <h2>Authorization</h2>
        <form action="auth_conf.php" method="post">
            <input type="text" name="login" placeholder="Enter your login" required>
            <input type="password" name="password" placeholder="Enter your password" required>
            <button type="submit">Log in</button>
            <p style="text-align:center; margin-top: 10px;">
            New here? <a href="register.php">Create an account</a>
            </p>
        </form>
</div>

<footer class="footer">
    <div class="container">
        <p>&copy; 2025 Balti24. All rights reserved.</p>
    </div>
</footer>
</body>
</html>
