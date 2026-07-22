<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>TRY20</title>
</head>
<body>
  <div>入力内容の取得</div>
  <?php
    if (isset( $_GET['name']) && $_GET['name'] !=""){
      print '入力した内容：' . htmlspecialchars($_GET['name'], ENT_QUOTES, 'UTF-8') . '<br>';
    } else {
      print '入力されていません<br>';
    }
    if (isset( $_GET['food']) && $_GET['food'] !=""){
      $food = implode(',', $_GET['food']);
      print '選択した内容：' . $food;
    } else {
      print '選択されていません';
    }
  ?>
</body>
</html>