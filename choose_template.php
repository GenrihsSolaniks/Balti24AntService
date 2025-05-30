<!-- choose_template.php -->
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Выбор шаблона акта</title>
</head>
<body>
  <h2>Выберите шаблон акта</h2>
  <form action="redirect_template.php" method="post">
    <label>
      <input type="radio" name="template" value="1" required>
      Шаблон 1: Клиент оплатил на месте
    </label><br>

    <label>
      <input type="radio" name="template" value="2">
      Шаблон 2: Подтверждение через Smart-ID
    </label><br>

    <label>
      <input type="radio" name="template" value="3">
      Шаблон 3: Фото документа с подписью
    </label><br><br>

    <button type="submit">Продолжить</button>
  </form>
</body>
</html>
