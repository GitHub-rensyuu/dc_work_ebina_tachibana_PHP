<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>TRY23</title>
</head>
<body>
  <form method="post">
    <?php
      $fp = fopen("file_write.txt","w"); //ファイルを開く
      // ファイルを一行ずつ取得する

      fwrite($fp,'ファイルへ書き込む'); //ファイルへ書き込み
      fclose($fp); //ファイルを閉じる
    ?>
  </form>
</body>
</html>