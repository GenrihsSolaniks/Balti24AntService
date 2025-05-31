<?php
session_start();
if (isset($_COOKIE['user'])) {
    header('Location: MainSite.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="stylelegacy.css">
    <style>
        .hidden { display: none; }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">Balti24</div>
            <nav class="nav">
                <ul>
                    <li class="me-3"><a href="index.html">Главная</a></li>
                    <li class="me-3"><a href="about.html">О нас</a></li>
                    <li class="me-3"><a href="MainSite.php">Заполнить форму заказа</a></li>
                    <li class="me-3"><a href="user_order.php">Время моих заказов</a></li>
                    <li class="me-3"><a href="contact.html">Контакты</a></l>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container form-container">
        <form action="register_conf.php" method="post">
            <h2>Регистрация</h2>
            <label>Login:<br><input type="text" name="login" required></label>
            <label>Password:<br><input type="password" name="password" required></label>
            <label>Email:<br><input type="email" name="email" required></label>
            <label>First Name:<br><input type="text" name="name" required></label>
            <label>Last Name:<br><input type="text" name="lastname" required></label>
            <label>Phone:<br><input type="text" name="phone" required></label>

            <label>Account Type:<br>
                <select name="account_type" id="account_type" onchange="toggleCompanyFields()" required>
                    <option value="individual">Физическое лицо</option>
                    <option value="juridical">Юридическое лицо</option>
                </select>
            </label>

            <div id="juridical-fields" class="hidden">
                <label>Company Name:<br><input type="text" name="company_name"></label>
                <label>Company Reg Number:<br><input type="text" name="company_reg_number"></label>
                <label>VAT Number:<br><input type="text" name="company_vat_number"></label>
                <label>Company Address:<br><textarea name="company_address"></textarea></label>
            </div>

            <button type="submit">Зарегистрироваться</button>
        </form>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Balti24. Все права защищены.</p>
        </div>
    </footer>

    <script>
        function toggleCompanyFields() {
            const type = document.getElementById('account_type').value;
            document.getElementById('juridical-fields').classList.toggle('hidden', type !== 'juridical');
        }
        window.onload = toggleCompanyFields;
    </script>
</body>
</html>
