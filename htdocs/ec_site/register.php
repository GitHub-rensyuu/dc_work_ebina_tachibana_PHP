<?php

// ==============================
// セッション開始
// ==============================
require_once __DIR__ . '/../../include/common/session.php';

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
// Model
// ==============================
require_once __DIR__ . '/../../include/model/user_model.php';

// ==============================
// 共通認証処理
// ==============================
require_once __DIR__ . '/../../include/common/auth.php';

// ==============================
// データベース接続
// ==============================
require_once __DIR__ . '/../../include/common/database.php';
$db = connect_database();

// ==============================
// 登録処理
// ==============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRFチェック
    verify_csrf_token();

    // ==============================
    // POSTデータ取得
    // ==============================
    $user_name = $_POST['user_name'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirmation = $_POST['password_confirmation'] ?? '';

    // ==============================
    // 入力チェック
    // ==============================
    if ($user_name === '' || $password === '' || $password_confirmation === '') {

        $_SESSION['register_error'] =
            'ユーザー名、パスワード、パスワード確認をすべて入力してください。';

        $_SESSION['register_user_name'] = $user_name;
        header('Location: register.php');
        exit();
    }

    // ==============================
    // ユーザー名チェック
    // ==============================
    if (
        strlen($user_name) < 5 ||
        !preg_match('/^[a-zA-Z0-9_]+$/', $user_name)
    ) {

        $_SESSION['register_error'] =
            'ユーザー名は5文字以上かつ半角英数字とアンダースコア（_）のみ登録可能です。';

        $_SESSION['register_user_name'] = $user_name;

        header('Location: register.php');
        exit();
    }

    // ==============================
    // パスワード文字数チェック
    // ==============================
    if (strlen($password) < 8) {

        $_SESSION['register_error'] =
            'パスワードは8文字以上で入力してください。';

        $_SESSION['register_user_name'] = $user_name;

        header('Location: register.php');
        exit();
    }

    // ==============================
    // パスワードの文字チェック
    // 半角英数字とアンダースコアのみ許可
    // ==============================
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $password)) {

        $_SESSION['register_error'] =
            'パスワードは8文字以上で、半角英数字とアンダースコア（_）のみ使用できます。';

        $_SESSION['register_user_name'] = $user_name;
        header('Location: register.php');
        exit();
    }

    // ==============================
    // パスワード確認
    // ==============================
    if ($password !== $password_confirmation) {

        $_SESSION['register_error'] =
            'パスワードとパスワード確認が一致しません。';

        $_SESSION['register_user_name'] = $user_name;
        header('Location: register.php');
        exit();
    }

    // ==============================
    // ユーザー名の重複確認
    // ==============================
    $user = find_user_by_user_name($db, $user_name);

    if ($user !== false) {

        $_SESSION['register_error'] =
            'そのユーザー名はすでに使用されています。';

        $_SESSION['register_user_name'] = $user_name;
        header('Location: register.php');
        exit();
    }

    // ==============================
    // ユーザー登録
    // ==============================
    try {

        $result = register_user($db, $user_name, $password);

    } catch (PDOException $e) {

        // 詳細なエラーはログに記録
        error_log($e->getMessage());

        // ユーザーには詳細を見せない
        $_SESSION['register_error'] =
            'ユーザー登録に失敗しました。';

        $_SESSION['register_user_name'] = $user_name;

        header('Location: register.php');
        exit();
    }


    // ==============================
    // 登録成功
    // ==============================
    $_SESSION['register_success'] =
        'ユーザー登録が完了しました。ログインしてください。';

    $_SESSION['register_user_name'] = $user_name;
    header('Location: index.php');
    exit();
}

// ==============================
// 登録エラー取得
// ==============================
if (isset($_SESSION['register_error'])) {
    $register_error = $_SESSION['register_error'];
    // 一度表示したら削除
    unset($_SESSION['register_error']);
} else {
    $register_error = '';
}

$user_name = $_SESSION['register_user_name'] ?? '';
unset($_SESSION['register_user_name']);

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ユーザー登録</title>

    <link rel="stylesheet" href="css/header.css">

    <style>

        body {
            margin: 0;
            min-height: 100vh;
        }

        .container {
            width: 400px;
            margin: 40px auto 0;
            text-align: center;
        }

        h1 {
            margin-bottom: 30px;
        }

        .register-error {
            margin-bottom: 20px;
            color: #d00;
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
            width: 130px;
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

        .register-button {
            width: 210px;
            height: 30px;

            background-color: #4c4cca;
            color: white;

            border: none;
            cursor: pointer;

            font-size: 16px;

            margin-top: 10px;
        }

        .login-link {
            display: block;
            margin-top: 10px;
            color: #333;
        }

    </style>
</head>

<body>

    <?php require_once __DIR__ . '/header.php'; ?>

    <div class="container">

        <h1>ユーザー登録</h1>

        <?php if ($register_error !== ''): ?>

            <div class="register-error">
                <?= htmlspecialchars($register_error, ENT_QUOTES, 'UTF-8') ?>
            </div>

        <?php endif; ?>

        <form action="register.php" method="post">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(
                    get_csrf_token(),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >

            <div class="form-row">
                <label for="user_name">
                    ユーザー名
                </label>

                <input
                    type="text"
                    id="user_name"
                    name="user_name"
                    value="<?= htmlspecialchars(
                        $user_name,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                    autocomplete="username"
                >
            </div>

            <div class="form-row">
                <label for="password">
                    パスワード
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="new-password"
                >
            </div>

            <div class="form-row">
                <label for="password_confirmation">
                    パスワード確認
                </label>

                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    autocomplete="new-password"
                >
            </div>

            <input
                type="submit"
                value="新規登録"
                class="register-button"
            >

        </form>

        <a href="index.php" class="login-link">
            ログインページへ
        </a>

    </div>

</body>
</html>
