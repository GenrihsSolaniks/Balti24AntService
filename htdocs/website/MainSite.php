<?php
if (!isset($_COOKIE['user'])) {
    header('Location: index.php'); // Перенаправление на index.php
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balti24 - Форма заказа</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styleform.css">
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
    <div class="container mt-4">
        <h1 class="text-center">Welcome, <?=htmlspecialchars($_COOKIE['user'])?>!</h1>
        <p class="text-center"><a href="exit.php" class="btn btn-link">Log out</a></p>

        <div class="app-container">
            <form action="order.php" method="post">
                <div class="form-group mb-3">
                    <label for="serviceArea">Select Service Area *</label>
                    <select id="serviceArea" name="ServiceArea" class="form-control" required>
                        <option value disabled selected>Please Select</option>
                        <option value="Cleaning">Cleaning</option>
                        <option value="Repair">Repair</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <input type="text" class="form-control" name="address" id="address" placeholder="Enter your address" required>
                </div>

                <div class="form-group mb-3">
                    <input type="text" class="form-control" name="city" id="city" placeholder="Enter your city" required>
                </div>

                <div class="form-group mb-3">
                    <label for="country">Select Country *</label>
                    <select id="country" name="country" class="form-control" required>
                        <option value disabled selected>Please Select</option>
                        <option value="Latvia">Latvia</option>
                        <option value="Germany">Germany</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="date">Select an Available Date *</label>
                    <input id="date" type="date" name="date" class="form-control" required>
                </div>

                <div class="form-group mb-3">
                    <label for="taskDescription">Describe the Task *</label>
                    <textarea id="taskDescription" placeholder="Provide details about the task" name="taskDescription" class="form-control" required></textarea>
                </div>

                <div class="form-group mb-3">
                    <label for="details">Additional Details (optional)</label>
                    <input type="text" class="form-control" name="details" id="details" placeholder="For example, access codes, parking, etc.">
                </div>

                <div class="form-group mb-3">
                    <label for="file">Attach a Photo</label>
                    <input id="file" type="file" class="form-control">
                </div>

                <div class="form-group mb-3">
                    <div class="form-check">
                        <input id="agreement1" type="checkbox" name="orderModel.AgreeToTerms" class="form-check-input" required>
                        <label for="agreement1" class="form-check-label">I accept the terms and conditions *</label>
                    </div>
                    <div class="form-check">
                        <input id="agreement2" type="checkbox" name="orderModel.AgreeToRefundPolicy" class="form-check-input" required>
                        <label for="agreement2" class="form-check-label">I accept the refund policy *</label>
                    </div>
                </div>

                <button class="btn btn-success w-100" type="submit">Submit Order</button>
            </form>
        </div>

        <p class="text-center mt-3">Other features in development and will be available soon.</p>
    </div>
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Balti24. Все права защищены.</p>
        </div>
    </footer>
</body>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const orderInput = document.getElementById("order");
        const detailsInput = document.getElementById("details");
        const submitButton = document.getElementById("submit");
        const errorMessage = document.getElementById("error");
      
        submitButton.addEventListener("click", (e) => {
          e.preventDefault();
      
          // Получение данных из полей
          const orderText = orderInput.value.trim();
          const detailsText = detailsInput.value.trim();
      
          // Проверка ограничений
          if (orderText.length > 300) {
            errorMessage.textContent = "Поле заказа не должно превышать 300 символов.";
            return;
          }
          if (detailsText.length > 100) {
            errorMessage.textContent = "Поле деталей не должно превышать 100 символов.";
            return;
          }
          if (orderText.includes("DROP DATABASE") || detailsText.includes("DROP DATABASE")) {
            errorMessage.textContent = "Нельзя вводить запрещенные команды.";
            return;
          }
      
          // Если всё корректно, выводим сообщение об успешной отправке
          errorMessage.textContent = "";
          alert("Заказ успешно отправлен!");
        });
      });
      
</script>
<style>
    /* Установка фона сайта и цвета текста */
    body {
        background-color: #ffffff; /* Белый фон */
        color: #000000; /* Чёрный текст */
    }

    form {
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #f9f9f9;
    }

    :root {
        --primary-color: #1b6ec2;
        --primary-hover-color: #145a99;
        --primary-border-color: #1861ac;
        --secondary-color: #258cfb;
        --error-color: #e50000;
        --success-color: #26b050;
    }

    a, .btn-link {
        color: var(--primary-color);
        text-decoration: none;
    }

    a:hover, .btn-link:hover {
        color: var(--primary-hover-color);
        text-decoration: underline;
    }

    .btn-primary {
        color: #fff;
        background-color: var(--primary-color);
        border-color: var(--primary-border-color);
    }

    .btn-primary:hover {
        background-color: var(--primary-hover-color);
        border-color: var(--primary-hover-color);
    }

    .app-container {
        margin-top: 2rem;
        padding: 2rem;
        background-color: #f8f9fa;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
</style>
</html>
