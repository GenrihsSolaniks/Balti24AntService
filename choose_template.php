<!-- choose_template.php -->
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Selecting an act template</title>
</head>
<body>
  <h2>Selecting an act template</h2>
  <form action="redirect_template.php" method="post">
    <label>
      <input type="radio" name="template" value="1" required>
      Template 1: Customer paid on the spot
    </label><br>

    <label>
      <input type="radio" name="template" value="2">
      Template 2: Confirmation via Smart-ID
    </label><br>

    <label>
      <input type="radio" name="template" value="3">
      Template 3: Photo of document with signature
    </label><br><br>

    <button type="submit">Continue</button>
  </form>
</body>
</html>
