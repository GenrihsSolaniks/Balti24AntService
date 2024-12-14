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
    <title>Registration form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col">
                <h1>Registration form</h1>
                <form action="check.php" method="post">
                    <input type="text" class="form-control" name="login" id="login" placeholder="Enter your login" required><br>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Enter your name" required><br>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" required><br>
                    <button class="btn btn-success" type="submit">Sign up</button>
                </form>
            </div>
            <div class="col">
                <h1>Authorization</h1>
                <form action="auth.php" method="post">
                    <input type="text" class="form-control" name="login" id="login" placeholder="Enter your login" required><br>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Enter your password" required><br>
                    <button class="btn btn-success" type="submit">Log in</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
