<?php
 $dsn = 'mysql:host=localhost;dbname=xb513874_g1gw7';
 $login_user = 'xb513874_h8646';
 $password = '1r86160zfh';
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>TRY46</title>
</head>
<body>
  <?php
    try{
      // データベースへ接続
      $db=new PDO($dsn,$login_user,$password);

      //PDOのエラー時にPDOExceptionが発生するように設定
      $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

      $db->beginTransaction(); //トラザクション開始

      //UPDATE文の実行
      $sql = "UPDATE product SET price = 160 WHERE product_id = 1";

      $result = $db->query($sql);
      $row = $result->rowCount();
      echo $row.'件更新しました。';
      $db->commit(); //正常に終了したらコミット

    } catch (PDOException $e){
      echo $e->getMessage();
      $db->rollBack(); //エラーが発生したらロールバック
    }
  ?>
</body>
</html>