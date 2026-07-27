<?php
  $host = 'localhost';
  $login_user = 'xb513874_h8646';
  $password = '1r86160zfh';
  $database = 'xb513874_g1gw7';
  $title = '';
  $lines = array();
  $file = 'data.txt';
  $error = '';
  $message = '';

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
      !empty($_POST['title'])
  ) {
      // 画像チェック
      if (
          !isset($_FILES['upload_image']) ||
          $_FILES['upload_image']['error'] !== UPLOAD_ERR_OK
      ) {
          $error = 'ファイルの形式が正しくありません（30KB以下のファイルのみを受け付けています。）';
      } else {
        // jpg・pngのみ許可
        $type = mime_content_type($_FILES['upload_image']['tmp_name']);

        if (!in_array($type, ['image/jpeg', 'image/png'])) {
            $error = 'ファイルの形式が正しくありません（jpgまたはpng形式の画像のみアップロードできます。）';
        } else {
              $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
              $filename = basename($_FILES['upload_image']['name']);
              $save = 'img/' . $filename;

              if (!is_dir('img')) {
                  mkdir('img', 0777, true);
              }
              if (move_uploaded_file($_FILES['upload_image']['tmp_name'], $save)) {
                  file_put_contents(
                      $file,
                      $title . '：' . $filename . PHP_EOL,
                      FILE_APPEND | LOCK_EX
                  );
                  $message = 'アップロード成功しました。';
              } else {
                  $error = 'アップロード失敗しました。';
              }
        }
      }
    } elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
      $error = '入力情報が不足しています';
  }

          
  // ファイルが存在すれば読み込む
  if (file_exists($file)) {
      // 新しく追加したものが最初に来るようにする
      $lines = file($file, FILE_IGNORE_NEW_LINES);
  }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>work30</title>
</head>
<body>
  <h1>画像投稿</h1>

  <!-- 投稿成否判定メッセージ -->
  <?php if (!empty($error)): ?>
    <p style="color:red;">
        <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
    </p>
  <?php endif; ?>

  <?php if (!empty($message)): ?>
    <p style="color:blue;">
        <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
    </p>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data">
    画像タイトル:<input type="text" name="title"><br>
    画像:<input type="file" name="upload_image" accept="image/jpeg,image/png"><br>
    <input type="submit" value="画像投稿">
  </form>

  <form method="post">
    <input type="submit" name="delete" value="投稿を全削除">
  </form>

  <a href="work30_gallery.php">画像一覧ページへ</a>

  <hr style="border:0; border-top:1px solid #bbb; width:100%; margin:20px 0;">

  <h2>投稿された画像</h2>

  <ul style="display:flex; flex-wrap:wrap; gap:30px; padding:0;">
    <?php foreach ($lines as $line): ?>
    <?php
        $data = explode('：', $line);
    ?>
    <li style="padding:30px 10px 10px; display:flex; flex-direction:column; align-items:center; list-style:none; border:1px solid #bbb;">
        <span>
            <?php echo htmlspecialchars($data[0], ENT_QUOTES, 'UTF-8'); ?>
        </span>

        <?php if (isset($data[1])): ?>
            <img
                src="img/<?php echo htmlspecialchars(trim($data[1]), ENT_QUOTES, 'UTF-8'); ?>"
                width="200"
                height="200"
                alt="投稿画像"
                style="margin-top:10px;"
            >
        <?php endif; ?>
    </li>

    <?php endforeach; ?>
  </ul>
</body>
</html>