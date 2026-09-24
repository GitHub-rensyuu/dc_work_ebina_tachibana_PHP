<?php

require_once __DIR__ . '/../../include/common/session.php';
require_once __DIR__ . '/../../include/common/auth.php';

// ログイン中の場合は権限に応じて遷移
if (isset($_SESSION['user_id'])) {
    if (
        isset($_SESSION['admin_flg']) &&
        (int)$_SESSION['admin_flg'] === 1
    ) {
        header('Location: admin_products.php');
        exit;
    }

    header('Location: products.php');
    exit;
}

// ログインエラーを取得
$login_error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);

// ユーザー登録成功メッセージを取得
$register_success = $_SESSION['register_success'] ?? '';
unset($_SESSION['register_success']);

// Cookieからユーザー名を取得
$remember_user_name = '';

if (
    isset($_COOKIE['remember_user_name']) &&
    $_COOKIE['remember_user_name'] === 'checked'
) {
    $remember_user_name = 'checked';
}

$user_name = $_COOKIE['user_name'] ?? '';

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ログインページ（トップページ）</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/index.css">
</head>

<body>

<?php require_once __DIR__ . '/header.php'; ?>

<div class="container">

    <h1>ログイン</h1>

    <?php if ($login_error !== ''): ?>
        <div class="login-error">
            <?= htmlspecialchars(
                $login_error,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </div>
    <?php endif; ?>

    <?php if ($register_success !== ''): ?>
        <div class="register-success">
            <?= htmlspecialchars(
                $register_success,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </div>
    <?php endif; ?>

    <form action="login.php" method="post">

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
            <label for="user_name">ユーザー名</label>

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
            <label for="password">パスワード</label>

            <input
                type="password"
                id="password"
                name="password"
                autocomplete="current-password"
            >
        </div>

        <div class="cookie-check">
            <input
                type="checkbox"
                id="remember_user_name"
                name="remember_user_name"
                value="checked"
                <?= $remember_user_name ?>
            >

            <label for="remember_user_name">
                次回からユーザー名の入力を省略する
            </label>
        </div>

        <input
            type="submit"
            value="ログイン"
            class="login-button"
        >

    </form>

    <a href="register.php" class="register-link">
        新規登録ページへ
    </a>

</div>

</body>
</html>