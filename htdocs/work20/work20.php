<?php
  $title = '';
  $content = '';
  $lines = array();
  $file = 'data.txt';

  if (isset($_POST['delete'])) {
    file_put_contents($file, '');

    foreach (glob('img/*') as $image) {
      if (is_file($image)) {
          unlink($image);
      }
    }
  }

  // タイトル・書き込み内容のチェック
  if (
      !empty($_POST['title']) &&
      !empty($_POST['content'])
  ) {
      // 画像チェック
      if (
          !isset($_FILES['upload_image']) ||
          $_FILES['upload_image']['error'] !== UPLOAD_ERR_OK
      ) {
          print 'ファイルの形式が正しくありません（このサンプルコードでは30KB以下のファイルのみを受け付けています。）';
      } else {
          // ファイルサイズチェック（30KB以下）
          if ($_FILES['upload_image']['size'] > 30000) {
              print 'ファイルの形式が正しくありません（このサンプルコードでは30KB以下のファイルのみを受け付けています。）';
          } else {
              $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
              $content = htmlspecialchars($_POST['content'], ENT_QUOTES, 'UTF-8');
              $filename = basename($_FILES['upload_image']['name']);
              $save = 'img/' . $filename;

              if (!is_dir('img')) {
                  mkdir('img', 0777, true);
              }

              if (move_uploaded_file($_FILES['upload_image']['tmp_name'], $save)) {
                  file_put_contents(
                      $file,
                      $title . '：' . $content . '：' . $filename . PHP_EOL,
                      FILE_APPEND | LOCK_EX
                  );
                  echo 'アップロード成功しました。';
              } else {
                  echo 'アップロード失敗しました。';
              }
          }
      }

  } elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
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
  <title>work20</title>
</head>
<body>
  <div>入力内容の取得</div>
  <form method="post" enctype="multipart/form-data">
    タイトル<br>
    <input type="text" name="title"><br>
    <br>
    書き込み内容<br>
    <input type="text" name="content"><br>
    <br>
    画像ファイル<br>
    <input type="file" name="upload_image"><br>
    <br>
    <input type="submit" value="送信"><br>
    <br>
  </form>

  <form method="post">
    <input type="submit" name="delete" value="投稿を全削除">
  </form>

  <ul>
    <?php foreach ($lines as $line): ?>
    <?php
        $data = explode('：', $line);
    ?>
    <li style="display:flex; align-items:center; gap:20px; margin-bottom:10px;">
      <span>
        <?php
          echo htmlspecialchars($data[0], ENT_QUOTES, 'UTF-8');
          echo '：';
          echo htmlspecialchars($data[1], ENT_QUOTES, 'UTF-8');
        ?>
      </span>

      <?php if (isset($data[2])): ?>
        <img src="img/<?php echo htmlspecialchars(trim($data[2]), ENT_QUOTES, 'UTF-8'); ?>" width="120" alt="投稿画像">
      <?php endif; ?>
    </li>

    <?php endforeach; ?>
  </ul>
</body>
</html>