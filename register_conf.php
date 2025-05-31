<?php
$mysqli = new mysqli('localhost', 'root', '', 'balti24db');
if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);
    $name = trim($_POST['name']);
    $last_name = trim($_POST['lastname']);
    $phone = trim($_POST['phone']);
    $account_type = $_POST['account_type'] ?? 'individual';

    // Данные юр. лица (если выбрано)
    $company_name = $_POST['company_name'] ?? null;
    $company_reg_number = $_POST['company_reg_number'] ?? null;
    $company_vat = $_POST['company_vat'] ?? null;
    $company_address = $_POST['company_address'] ?? null;

    // Проверка на обязательные поля
    if (empty($login) || empty($password) || empty($email) || empty($last_name) || empty($phone)) {
        die("Пожалуйста, заполните все обязательные поля.");
    }

    // Проверка уникальности логина
    $stmt = $mysqli->prepare("SELECT id FROM users WHERE login = ?");
    $stmt->bind_param('s', $login);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        die("Такой логин уже существует.");
    }

    //$hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare("
        INSERT INTO users (login, password, email, name, lastname, phone, account_type, company_name, company_reg_number, company_vat, company_address)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        'sssssssssss',
        $login,
        $password,
        $email,
        $name,
        $last_name,
        $phone,
        $account_type,
        $company_name,
        $company_reg_number,
        $company_vat,
        $company_address
    );

    if ($stmt->execute()) {
        echo "✅ Регистрация прошла успешно! <a href='index.php'>На главную</a>";
    } else {
        echo "❌ Ошибка регистрации: " . $stmt->error;
    }
}
?>
