<?php
 $dsn = 'mysql:host=localhost;dbname=xb513874_g1gw7';
 $login_user = 'xb513874_h8646';
 $password = '1r86160zfh';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>work31</title>
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
    $sql = "SELECT product.product_name,category.category_name 
            FROM product INNER JOIN category ON product.category_id = category.category_id
            WHERE product.category_id = 1;";
    if ($result = $db->query($sql)){
      //連想配列を取得
      while ($row = $result->fetch()){
        echo $row["product_name"] . $row["category_name"] . "<br>";
      }
    }
  ?>
</body>
</html>