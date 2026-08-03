<?php
 $dsn = 'mysql:host=localhost;dbname=xb513874_g1gw7';
 $login_user = 'xb513874_h8646';
 $password = '1r86160zfh';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>work33</title>
</head>
<body>
  <?php
    try{
      // データベースへ接続
      $db=new PDO($dsn,$login_user,$password);
    } catch (PDOException $e){
      echo $e->getMessage();
      exit();
    }
    // SELECT文の実行
    $sql = "SELECT product_name, price FROM product WHERE price <= ?";

    //prepareメソッドによるクエリの実行準備をする
    $stmt = $db->prepare($sql);

    //値をバインドする
    $stmt->bindValue(1, 100);

    //クエリの実行
    $stmt->execute();

    // 実行結果を取得
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row["product_name"] . " " . $row["price"] . "<br>";
    }

  ?>
</body>
</html>