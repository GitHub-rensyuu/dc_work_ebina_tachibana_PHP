<?php
  $check_data =''; //初期化
  if (isset($_POST['check_data'])){
    $check_data = htmlspecialchars($_POST['check_data'], ENT_QUOTES, 'UTF-8');
  }
  $check_data2 =''; //初期化
  if (isset($_POST['check_data2'])){
    $check_data2 = htmlspecialchars($_POST['check_data2'], ENT_QUOTES, 'UTF-8');
  }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>work21</title>
</head>
<body>
  <form method="post">
    <div>半角アルファベットの大文字・小文字で入力を行ってください。</div>
    <input type="text" name="check_data" value= <?php echo $check_data ?>>
    <input type="submit" value="送信">
  </form>
  <form method="post">
    <div>ハイフンありで携帯電話番号の入力を行ってください。</div>
    <input type="text" name="check_data2" value= <?php echo $check_data2 ?>>
    <input type="submit" value="送信">
  </form>
  <?php
    // input①
    // 小文字・大文字のアルファベットに一致
    if (!preg_match("/^[a-zA-Z]+$/", $check_data) && $check_data !== ''){
      echo "<div>正しい入力形式ではありません</div>";
      // 入力した文字列に「dc」が含まれている
      } elseif(preg_match("/dc/", $check_data)){
        echo "<div>ディーキャリアが含まれています</div>";
        // 入力した文字列が「end」で終わっている
      } elseif(preg_match("/end$/", $check_data)){
      echo "<div>終了です！</div>";
    }
    // input②
    if (!preg_match("/^0[789]0-[0-9]{4}-[0-9]{4}$/", $check_data2) && $check_data2 !== ''){
      echo "<div>携帯電話番号の形式ではありません</div>";
    }
  ?>
</body>
</html>