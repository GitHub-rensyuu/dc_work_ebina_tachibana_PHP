<?php
  $name = '';
  if (isset($_POST['name'])){
    $name = htmlspecialchars($_POST['name'],ENT_QUOTES, 'UTF-8');
  }
  $food = '';
  if (isset($_POST['food'])) {
      $food = implode(
          ',',
          array_map(
              fn($item) => htmlspecialchars($item, ENT_QUOTES, 'UTF-8'),
              $_POST['food']
          )
      );
  }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>work17</title>
</head>
<body>
  <div>入力内容の取得</div>
  <form method="post">
    <input type="text" name="name">
    <input type="checkbox" name="food[]" value="選択肢01"> 選択肢01
	  <input type="checkbox" name="food[]" value="選択肢02"> 選択肢02
	  <input type="checkbox" name="food[]" value="選択肢03"> 選択肢03
    <input type="submit" value="送信">
  </form>
  
  <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
    <div>入力した内容は「<?php echo $name; ?>」です。</div>
    <div>選択した内容は「<?php echo $food; ?>」です。</div>
  <?php endif; ?>
</body>
</html>