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
    <title>Balti24 - Order form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styleform.css">
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
    <div class="container mt-4">
        <h1 class="text-center">Welcome, <?=htmlspecialchars($_COOKIE['user'])?>!</h1>
        <p class="text-center"><a href="exit_conf.php" class="btn btn-link">Log out</a></p>

        <div class="app-container">
            <form action="order_conf.php" method="post" enctype="multipart/form-data">
                <div class="form-group mb-3">
                    <label for="serviceArea">Select Service Area *</label>
                    <select id="serviceArea" name="ServiceArea" class="form-control" required>
                        <option value disabled selected>Please Select</option>
                        <option value="Cleaning">Cleaning</option>
                        <option value="Repair">Repair</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <p>Phone format: +371 12345678</p>
                    <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter your phone nr." required>
                </div>

                <div class="form-group mb-3">
                    <input type="text" class="form-control" name="email" id="email" placeholder="Enter your email" required>
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
                    <p id="date-warning" style="color: red; display: none;">This day is already taken! Choose another.</p>
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
                    <input id="file" type="file" class="form-control" name="photo">
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
            <p>&copy; 2025 Balti24. All rights reserved.</p>
        </div>
    </footer>
    <script>
document.addEventListener("DOMContentLoaded", function () {
    let dateInput = document.getElementById("date");
    let serviceAreaInput = document.getElementById("serviceArea");
    let dateWarning = document.getElementById("date-warning");

    // ✅ Устанавливаем минимальную дату (нельзя выбрать прошедшие дни)
    let today = new Date().toISOString().split("T")[0];
    dateInput.setAttribute("min", today);

    // Загружаем занятые даты
    fetch("get_busy_dates.php")
        .then(response => response.json())
        .then(busyDates => {
            dateInput.addEventListener("input", function () {
                let selectedDate = this.value;
                let selectedService = serviceAreaInput.value; // Получаем тип работы
                
                // Если сервис не выбран - запрещаем выбор даты
                if (!selectedService) {
                    alert("First, choose your area of work!");
                    this.value = "";
                    return;
                }

                // Проверяем, занята ли дата для выбранного типа работы
                if (busyDates[selectedDate] && busyDates[selectedDate].includes(selectedService)) {
                    dateWarning.style.display = "block"; // Показываем предупреждение
                    this.value = ""; // Очищаем поле
                } else {
                    dateWarning.style.display = "none"; // Скрываем предупреждение
                }
            });
        })
        .catch(error => console.error("Error loading busy dates:", error));
});
</script>

</body>
</html>
