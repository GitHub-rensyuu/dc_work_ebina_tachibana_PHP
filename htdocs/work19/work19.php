<?php
  $title = '';
  $content = '';
  $lines = array();
  $file = 'data.txt';

  if (isset($_GET['title']) && isset($_GET['content']) && $_GET['title'] != "" && $_GET['content'] != ""){
    $title = htmlspecialchars($_GET['title'],ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars($_GET['content'],ENT_QUOTES, 'UTF-8');

    // ファイルに追記する
    file_put_contents(
        $file,
        $title . '：' . $content . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );
  } else {
    print '入力情報が不足しています';
  }

  // ファイルが存在すれば読み込む
  if (file_exists($file)) {
      // 新しく追加したものが最初に来るようにする
      $lines = array_reverse(file($file, FILE_IGNORE_NEW_LINES));
  }

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>work19</title>
</head>
<body>
  <div>入力内容の取得</div>
  <form method="get">
    「タイトル」入力欄<br>
    <input type="text" name="title"><br>
    「書き込み内容」入力欄<br>
    <input type="text" name="content"><br>
    <br>
    <input type="submit" value="送信">
  </form>
  
  <?php if ($_SERVER["REQUEST_METHOD"] == "GET"): ?>
    <ul>
      <?php foreach ($lines as $line): ?>
      <li><?php echo $line; ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</body>
</html>