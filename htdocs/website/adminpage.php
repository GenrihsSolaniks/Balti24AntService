<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-Time MySQL Data</title>
    <style>
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <h1 style="text-align:center;">Real-Time MySQL Data</h1>
    <div id="data-container">
        <!-- Таблица будет загружаться сюда -->
    </div>

    <script>
        // Функция для загрузки данных
        function loadData() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "fetch_data.php", true);
            xhr.onload = function() {
                if (this.status === 200) {
                    document.getElementById('data-container').innerHTML = this.responseText;
                }
            }
            xhr.send();
        }

        // Загружаем данные при загрузке страницы
        loadData();

        // Обновляем данные каждые 5 секунд
        setInterval(loadData, 5000);
    </script>
</body>
</html>
