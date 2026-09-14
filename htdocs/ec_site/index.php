<?php
    // ==============================
    // セッション開始
    // ==============================
    session_start();

    // ==============================
    // ログイン中か確認
    // ==============================
    if (isset($_SESSION['user_id'])) {

        // 管理者
        if (
            isset($_SESSION['admin_flg']) &&
            $_SESSION['admin_flg'] === 1
        ) {
            header('Location: admin_products.php');
            exit();
        }

        // 一般ユーザー
        header('Location: products.php');
        exit();
    }

    // ==============================
    // ログインエラーを取得
    // ==============================
    if (isset($_SESSION['login_error'])) {

        $login_error = $_SESSION['login_error'];

        // 一度表示したら削除
        unset($_SESSION['login_error']);

    } else {

        $login_error = '';
    }

    // ==============================
    // ユーザー登録成功メッセージを取得
    // ==============================
    if (isset($_SESSION['register_success'])) {

        $register_success = $_SESSION['register_success'];

        // 一度表示したら削除
        unset($_SESSION['register_success']);

    } else {

        $register_success = '';
    }

    // ==============================
    // CookieからユーザーIDを取得
    // ==============================
    if (isset($_COOKIE['cookie_confirmation'])) {
        $cookie_confirmation = 'checked';
    } else {
        $cookie_confirmation = '';
    }

    if (isset($_COOKIE['user_name'])) {
        $user_name = $_COOKIE['user_name'];
    } else {
        $user_name = '';
    }

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ログインページ（トップページ）</title>
    <link rel="stylesheet" href="css/header.css">
    <style>

        body {
            margin: 0;
            min-height: 100vh;

        }

        .container {
            width: 400px;
            margin:40px auto 0;
            text-align: center;
        }

        h1 {
            margin-bottom: 30px;
        }

        .login-error {
            margin-bottom: 20px;
            color: #d00;
            font-weight: bold;
        }

        .register-success {
            margin-bottom: 20px;
            color: #080;
            font-weight: bold;
        }

        form {
            width: 100%;
        }

        .form-row {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .form-row label {
            width: 90px;
            text-align: right;
            margin-right: 10px;
            font-weight: bold;
        }

        .form-row input[type="text"],
        .form-row input[type="password"] {
            width: 210px;
            height: 28px;
            box-sizing: border-box;
        }

        .cookie-check {
            margin-top: 15px;
            margin-bottom: 20px;
        }

        .login-button {
            width: 210px;
            height: 30px;

            background-color: #4c4cca;
            color: white;

            border: none;
            cursor: pointer;

            font-size: 16px;
        }

        .register-link {
            display: block;
            margin-top: 10px;
            color: #333;
        }



    </style>
</head>

<body>
    <?php require_once __DIR__ . '/header.php'; ?>

    <div class="container">
        <h1>ログイン</h1>

        <?php if ($login_error !== ''): ?>
            <div class="login-error">
                <?= htmlspecialchars($login_error,ENT_QUOTES,'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if ($register_success !== ''): ?>
            <div class="register-success">
                <?= htmlspecialchars($register_success, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="post">

            <div class="form-row">
                <label for="user_name">ユーザー名</label>
                <input type="text" id="user_name" name="user_name" value="<?php
                        echo htmlspecialchars($user_name, ENT_QUOTES, 'UTF-8');
                    ?>"
                >
            </div>

            <div class="form-row">
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password">
            </div>

            <div class="cookie-check">
                <input type="checkbox" name="cookie_confirmation" value="checked"
                    <?php echo $cookie_confirmation; ?>
                >
                次回からユーザー名の入力を省略する
            </div>

            <input type="submit" value="ログイン" class="login-button">
        </form>

        <a href="register.php" class="register-link">新規登録ページへ</a>
    </div>
</body>
</html>