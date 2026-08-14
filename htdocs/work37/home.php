<?php
  // データベース接続情報
  $host = 'localhost';
  $login_user = 'xb513874_h8646';
  $password = '1r86160zfh';
  $database = 'xb513874_g1gw7';

  // データベースへ接続
  $db = new mysqli($host, $login_user, $password, $database);
  if ($db->connect_error) {
    die($db->connect_error);
  }
  $db->set_charset("utf8");

  // Cookieの保存期間
  define('EXPIRATION_PERIOD', 30);
  $cookie_expiration = time() + EXPIRATION_PERIOD * 60 * 24 * 365;

  // POSTされた値を変数に格納する
  if (isset($_POST['cookie_confirmation'])) {
    $cookie_confirmation = $_POST['cookie_confirmation'];
  } else {
    $cookie_confirmation = '';
  }

  if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
  } else {
    $user_id = '';
  }

  if (isset($_POST['password'])) {
    $input_password = $_POST['password'];
  } else {
    $input_password = '';
  }

  // user_idとpasswordがデータベースに存在するか確認
  $user_name = '';
  if ($user_id !== '' && $input_password !== '') {
    $sql = 'SELECT user_name FROM user_table
            WHERE user_id = ? AND password = ?';
    $stmt = $db->prepare($sql);
    if ($stmt === false) {
      die('SQLエラー：' . $db->error);
    }
    $stmt->bind_param('ss', $user_id, $input_password);
    $stmt->execute();
    $stmt->bind_result($user_name);
    $login_success = $stmt->fetch();
    $stmt->close();
  } else {
    $login_success = false;
  }

  // ログインに成功した場合だけCookieを保存
  if ($login_success === true) {
    if ($cookie_confirmation === 'checked') {
      setcookie(
        'cookie_confirmation',
        $cookie_confirmation,
        $cookie_expiration
      );
      setcookie(
        'user_id',
        $user_id,
        $cookie_expiration
      );

    } else {

      // Cookieを削除
      setcookie('cookie_confirmation', '', time() - 30);
      setcookie('user_id', '', time() - 30);

    }
  }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>TRY53</title>
</head>

<body>
  <?php if ($login_success === true): ?>
    <p>ログイン（疑似的）が完了しました</p>
    <p>
      <?php echo htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8'); ?>さん、ようこそ！
    </p>
  <?php else: ?>
    <p>ログインに失敗しました。</p>
  <?php endif; ?>
  <p><a href="work37.php">戻る</a></p>

</body>
</html>
