<?php
if (isset($_COOKIE['user'])) {
    // Если пользователь авторизован, покажем ему выбор
    echo "<p>Hello, " . htmlspecialchars($_COOKIE['user']) . "!</p>";
    echo "<p>You are already logged in. Go to <a href='MainSite.php'>Main Site</a> or <a href='exit.php'>Log out</a>.</p>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balti24 - Регистрация</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="header">
        <div class="container">
            <div class="logo">Balti24</div>
            <nav class="nav">
                <ul>
                    <li><a href="index.html">Главная</a></li>
                    <li><a href="about.html">О нас</a></li>
                    <li><a href="MainSite.php">Заполнить форму заказа</a></li>
                    <li><a href="contact.html">Контакты</a></li>
                </ul>
            </nav>
        </div>
    </header>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col">
                <h1>Registration form</h1>
                <h3>Please choose your type of account</h3>
                <ul>
                    <li><a href="compregister.html">Юридическое лицо</a></li>
                    <li><a href="indregister.html">Физическое лицо</a></li>
                </ul>
               
            </div>
            <div class="col">
                <h1>Authorization</h1>
                <form action="auth_conf.php" method="post">
                    <input type="text" class="form-control" name="login" id="login" placeholder="Enter your login" required><br>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" required><br>
                    <button class="btn btn-success" type="submit">Log in</button>
                </form>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Balti24. Все права защищены.</p>
        </div>
    </footer>
</body>
</html>
