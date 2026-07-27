<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>TRY37</title>
</head>
<body>
  <?php
    $db = new mysqli('localhost', 'xb513874_h8646', '1r86160zfh', 'xb513874_g1gw7');
    if ($db->connect_error){
      echo $db->connect_error;
      exit();
    } else {
      print("データベースへの接続に成功しました。");
    }
    $db->close();
  ?>

</body>
</html>