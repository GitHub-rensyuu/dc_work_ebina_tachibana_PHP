<?php
  // データベース接続情報
  $host = 'localhost';
  $login_user = 'xb513874_h8646';
  $password = '1r86160zfh';
  $database = 'xb513874_g1gw7';
 
  // データベースへ接続、文字コード設定
  $db = new mysqli($host, $login_user, $password, $database);
  if ($db->connect_error) {
      die($db->connect_error);
  }
  $db->set_charset("utf8");
  
  // 投稿タイトル、エラーメッセージ、成功メッセージ、投稿一覧の変数を設定
  $title = '';
  $error = '';
  $message = '';
  $posts = [];

  // 全削除ボタンを押したときに、imageテーブルの全データ削除
  if (isset($_POST['deleteAll'])) {
      $sql = "DELETE FROM image";

      if (!$db->query($sql)) {
          $error = "削除エラー：" . $db->error;
      }

      // imgフォルダ内の全ファイルを削除
      foreach (glob('img/*') as $image) {
          if (is_file($image)) {
              unlink($image);
          }
      }
      $message = "全削除しました。";
  }

  // 削除ボタンを押したときに、imageテーブルのデータ削除
// 個別削除
  if (isset($_POST['delete'])) {

      $image_id = (int)$_POST['image_id'];

      // 削除する画像名を取得
      $sql = "SELECT file_name FROM image WHERE image_id = ?";
      $stmt = $db->prepare($sql);
      $stmt->bind_param("i", $image_id);
      $stmt->execute();

      $result = $stmt->get_result();
      $image = $result->fetch_assoc();

      if ($image) {
          $sql = "DELETE FROM image WHERE image_id = ?";
          $stmt = $db->prepare($sql);
          $stmt->bind_param("i", $image_id);

          if ($stmt->execute()) {
              // imgフォルダから削除
              $path = "img/" . $image['file_name'];

              if (file_exists($path)) {
                  unlink($path);
              }
              $message = "画像を削除しました。";
          } else {
              $error = "削除に失敗しました。";
          }

      } else {
          $error = "画像が見つかりません。";
      }
  }


  // タイトル・書き込み内容のチェック
  if (!empty($_POST['title'])&& !isset($_POST['deleteAll']) && !isset($_POST['change_public']) && !isset($_POST['delete'])) {
      // 画像チェック
      if (
          !isset($_FILES['upload_image']) ||
          $_FILES['upload_image']['error'] !== UPLOAD_ERR_OK
      ) {
          $error = '画像ファイルを選択してください。';
      } else {
        // jpg・pngのみ許可
        $type = mime_content_type($_FILES['upload_image']['tmp_name']);
        if (!in_array($type, ['image/jpeg', 'image/png'])) {
            $error = 'ファイルの形式が正しくありません（jpgまたはpng形式の画像のみアップロードできます。）';
        } else {
          $title = $_POST['title'];
          $filename = basename($_FILES['upload_image']['name']);
          $extension = pathinfo($filename, PATHINFO_EXTENSION);
          // 画像ファイルをユニークな名前に変換し、重複しないようにする
          $new_filename = uniqid() . '.' . $extension;
          $save = 'img/' . $new_filename;

          // imgフォルダが無ければ作成する
          if (!is_dir('img')) {
              mkdir('img', 0777, true);
          }

          // 画像ファイルを一時フォルダからimgフォルダに移動する
          if (move_uploaded_file($_FILES['upload_image']['tmp_name'], $save)) {
            $sql = "INSERT INTO image(title, file_name) VALUES(?, ?)";
            $stmt = $db->prepare($sql);

            // SQL文が正しいか確認
            if (!$stmt) {
              unlink($save);
              $error = $db->error;

            } else {
              $stmt->bind_param("ss", $title, $new_filename);
              if ($stmt->execute()) {
                  $message = 'アップロード成功しました。';
              } else {
                  // INSERT処理に失敗した場合、保存した画像を削除
                  unlink($save);
                  $error = $stmt->error;
              }
            }

          } else {
            $error = 'アップロード失敗しました。';
          }
        }
      }
  } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['deleteAll']) && !isset($_POST['change_public']) && !isset($_POST['delete'])) {
    $error = '入力情報が不足しています';
  }
  
// 公開・非公開切り替え
  if(isset($_POST['change_public'])){
      if(isset($_POST['image_id']) && isset($_POST['public_flg'])){
        $image_id = $_POST['image_id'];
        $public_flg = $_POST['public_flg'];
      }

      $new_flg = ($public_flg == 1) ? 0 : 1;

      $sql = "UPDATE image SET public_flg=? WHERE image_id=?";
      $stmt = $db->prepare($sql);
      $stmt->bind_param("ii", $new_flg, $image_id);
      $stmt->execute();

      if ($new_flg == 1) {
        $message = "公開しました。";
      } else {
        $message = "非公開にしました。";
      }
  }

  // imageの画像を表示
  $result = $db->query("SELECT * FROM image ORDER BY image_id ASC");
  $posts = $result->fetch_all(MYSQLI_ASSOC);
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
  <div style="min-height:24px;">
    <?php if (!empty($error)): ?>
      <p style="color:red; margin:0;">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
      </p>
    <?php elseif (!empty($message)): ?>
      <p style="color:blue; margin:0;">
        <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
      </p>
    <?php endif; ?>
  </div>

  <form method="post" enctype="multipart/form-data">
    画像タイトル:<input type="text" name="title"><br>
    画像:<input type="file" name="upload_image" accept="image/jpeg,image/png"><br>
    <input type="submit" value="画像投稿">
  </form>

  <form method="post">
    <input type="submit" name="deleteAll" value="投稿を全削除">
  </form>

  <a href="work30_gallery.php">画像一覧ページへ</a>

  <hr style="border:0; border-top:1px solid #bbb; width:100%; margin:20px 0;">

  <h2>投稿された画像</h2>

  <ul style="display:flex; flex-wrap:wrap; gap:30px; padding:0;">
    <?php foreach ($posts as $post): ?>

    <li style="padding:30px 10px 10px;
      display:flex;
      flex-direction:column;
      align-items:center;
      list-style:none;
      border:1px solid #bbb;
      background-color: <?= $post['public_flg'] == 0 ? '#ddd' : '#fff' ?>;
    ">
        <span>
            <?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>
        </span>

        <img
            src="img/<?php echo htmlspecialchars($post['file_name'], ENT_QUOTES, 'UTF-8'); ?>"
            width="200"
            height="200"
            style="margin-top:10px;"
            alt="投稿画像"
        >

        <form method="post">
            <input type="hidden" name="image_id" value="<?= $post['image_id'] ?>">
            <input type="hidden" name="public_flg" value="<?= $post['public_flg'] ?>">

            <?php if($post['public_flg'] == 1): ?>
                <input type="submit" name="change_public" value="非表示にする">
            <?php else: ?>
                <input type="submit" name="change_public" value="表示する">
            <?php endif; ?>
        </form>

        <form method="post">
          <input type="hidden" name="image_id" value="<?= $post['image_id'] ?>">
          <input type="submit" name="delete" value="削除" onclick="return confirm('この画像を削除しますか？');">
      </form>

    </li>
    <?php endforeach; ?>
  </ul>
</body>
</html>