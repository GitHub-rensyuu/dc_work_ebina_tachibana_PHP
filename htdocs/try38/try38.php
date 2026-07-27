<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>TRY38</title>
</head>
<body>
  <?php
    $db = new mysqli('localhost', 'xb513874_h8646', '1r86160zfh', 'xb513874_g1gw7');
    if ($db->connect_error){
      echo $db->connect_error;
      exit();
    } else {
      $db->set_charset("utf8"); //文字コードをUTF8に指定
    }

    $sql = "SELECT product_name, price FROM product WHERE price <= 100";

    if ($result = $db->query($sql)) {
      // 連想配列を取得
      while ($row = $result->fetch_assoc()){
        echo $row["product_name"] . $row["price"] . "<br>";
      }

      // 結果セットを閉じる
      $result->close();
    }

    $db->close();
  ?>

</body>
</html>