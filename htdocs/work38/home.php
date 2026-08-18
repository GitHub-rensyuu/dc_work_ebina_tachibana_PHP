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
// Cookieの保存期間
// ==============================
define('EXPIRATION_PERIOD', 30);

$cookie_expiration =
    time() + EXPIRATION_PERIOD * 60 * 24 * 365;


// ==============================
// ログアウト処理
// ==============================
//
// 「戻る」ボタンが押された場合
//
if (isset($_POST['logout'])) {

    // ------------------------------
    // ユーザーIDを保存
    // ------------------------------
    //
    // ログアウト後もuser_idだけ残す
    //
    if (isset($_SESSION['login_id'])) {
        $logout_user_id = $_SESSION['login_id'];

        setcookie(
            'user_id',
            $logout_user_id,
            $cookie_expiration
        );
    }


    // ------------------------------
    // cookie_confirmationを削除
    // ------------------------------
    setcookie(
        'cookie_confirmation',
        '',
        time() - 3600
    );


    // ------------------------------
    // セッション変数を削除
    // ------------------------------
    $_SESSION = [];


    // ------------------------------
    // セッションCookieを削除
    // ------------------------------
    if (ini_get("session.use_cookies")) {

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }


    // ------------------------------
    // セッションを破棄
    // ------------------------------
    session_destroy();


    // ------------------------------
    // work38.phpへ戻る
    // ------------------------------
    header('Location: work38.php');
    exit();
}


// ==============================
// ログイン処理
// ==============================

$login_success = false;
$user_name = '';


// POSTされた場合
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ------------------------------
    // Cookie保存チェック
    // ------------------------------
    if (isset($_POST['cookie_confirmation'])) {
        $cookie_confirmation =
            $_POST['cookie_confirmation'];
    } else {
        $cookie_confirmation = '';
    }


    // ------------------------------
    // ユーザーID
    // ------------------------------
    if (isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
    } else {
        $user_id = '';
    }


    // ------------------------------
    // パスワード
    // ------------------------------
    if (isset($_POST['password'])) {
        $input_password = $_POST['password'];
    } else {
        $input_password = '';
    }


    // ------------------------------
    // ユーザー情報を確認
    // ------------------------------
    if (
        $user_id !== '' &&
        $input_password !== ''
    ) {

        $sql = '
            SELECT user_name
            FROM user_table
            WHERE user_id = ?
            AND password = ?
        ';

        $stmt = $db->prepare($sql);

        if ($stmt === false) {
            die('SQLエラー：' . $db->error);
        }

        $stmt->bind_param(
            'ss',
            $user_id,
            $input_password
        );

        $stmt->execute();

        $stmt->bind_result($user_name);

        $login_success = $stmt->fetch();

        $stmt->close();
    }


    // ------------------------------
    // ログイン成功
    // ------------------------------
    if ($login_success === true) {

        // セッションIDを再生成
        session_regenerate_id(true);

        // ログインユーザーIDをセッションに保存
        $_SESSION['login_id'] = $user_id;


        // ------------------------------
        // Cookie保存
        // ------------------------------
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

            // cookie_confirmationだけ削除
            setcookie(
                'cookie_confirmation',
                '',
                time() - 3600
            );

            // user_idも削除
            setcookie(
                'user_id',
                '',
                time() - 3600
            );
        }
    }
}


// ==============================
// ログイン状態を確認
// ==============================
//
// 未ログインでhome.phpにアクセスした場合
// work38.phpへ移動
//
if (!isset($_SESSION['login_id'])) {

    header('Location: work38.php');
    exit();
}


// ==============================
// ログイン中のユーザー情報を取得
// ==============================

$login_id = $_SESSION['login_id'];

$sql = '
    SELECT user_name
    FROM user_table
    WHERE user_id = ?
';

$stmt = $db->prepare($sql);

if ($stmt === false) {
    die('SQLエラー：' . $db->error);
}

$stmt->bind_param(
    's',
    $login_id
);

$stmt->execute();

$stmt->bind_result($user_name);

$user_found = $stmt->fetch();

$stmt->close();


// ユーザーがDBに存在しない場合
if (!$user_found) {

    $_SESSION = [];

    session_destroy();

    header('Location: work38.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>home</title>
</head>

<body>

<?php
// ==============================
// ログイン中のユーザーを表示
// ==============================

echo '<p>';

echo htmlspecialchars(
    $user_name,
    ENT_QUOTES,
    'UTF-8'
);

echo 'さん：ログイン中です';

echo '</p>';


// ==============================
// ログイン直後のメッセージ
// ==============================

if ($login_success === true) {
?>

    <p>ログイン（疑似的）が完了しました。</p>

    <p>
        <?php
        echo htmlspecialchars(
            $user_name,
            ENT_QUOTES,
            'UTF-8'
        );
        ?>さん、ようこそ！
    </p>

<?php
}
?>


<!-- ============================== -->
<!-- 戻るボタン（ログアウト） -->
<!-- ============================== -->

<form action="home.php" method="post">

    <input
        type="submit"
        name="logout"
        value="戻る"
    >

</form>

</body>
</html>