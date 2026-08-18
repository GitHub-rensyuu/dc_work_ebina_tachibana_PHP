<?php
// ==============================
// セッション開始
// ==============================
session_start();

// ==============================
// データベース接続情報
// ==============================
$host = 'localhost';
$login_user = 'xb513874_h8646';
$password = '1r86160zfh';
$database = 'xb513874_g1gw7';

// ==============================
// データベースへ接続
// ==============================
$db = new mysqli(
    $host,
    $login_user,
    $password,
    $database
);

if ($db->connect_error) {
    die($db->connect_error);
}

$db->set_charset("utf8");


// ==============================
// ログイン中か確認
// ==============================
// ログイン中ならhome.phpへ移動
if (isset($_SESSION['login_id'])) {
    header('Location: home.php');
    exit();
}


// ==============================
// CookieからユーザーIDを取得
// ==============================
if (isset($_COOKIE['cookie_confirmation'])) {
    $cookie_confirmation = 'checked';
} else {
    $cookie_confirmation = '';
}

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>work38</title>
</head>

<body>

<h1>WORK38：擬似ログイン</h1>

<form action="home.php" method="post">

    <label for="user_id">ユーザーID：</label>

    <input
        type="text"
        id="user_id"
        name="user_id"
        value="<?php
            echo htmlspecialchars(
                $user_id,
                ENT_QUOTES,
                'UTF-8'
            );
        ?>"
    >

    <br>

    <label for="password">パスワード：</label>

    <input
        type="password"
        id="password"
        name="password"
    >

    <br>

    <input
        type="checkbox"
        name="cookie_confirmation"
        value="checked"
        <?php echo $cookie_confirmation; ?>
    >

    次回からユーザーIDの入力を省略する（Cookie保存）

    <br>

    <input type="submit" value="ログイン">

</form>

</body>
</html>