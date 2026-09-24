<?php

require_once __DIR__ . '/../../include/common/session.php';
require_once __DIR__ . '/../../include/common/auth.php';
require_once __DIR__ . '/../../include/model/user_model.php';
require_once __DIR__ . '/../../include/common/database.php';

$db = connect_database();

// ログイン中の場合は遷移
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

// POST処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();

    $user_name = $_POST['user_name'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirmation = $_POST['password_confirmation'] ?? '';

    // 必須チェック
    if (
        $user_name === '' ||
        $password === '' ||
        $password_confirmation === ''
    ) {
        $_SESSION['register_error'] =
            'ユーザー名、パスワード、パスワード確認をすべて入力してください。';

        $_SESSION['register_user_name'] = $user_name;

        header('Location: register.php');
        exit;
    }

    // ユーザー名チェック
    if (
        strlen($user_name) < 5 ||
        !preg_match('/^[a-zA-Z0-9_]+$/', $user_name)
    ) {
        $_SESSION['register_error'] =
            'ユーザー名は5文字以上かつ半角英数字とアンダースコア（_）のみ登録可能です。';

        $_SESSION['register_user_name'] = $user_name;

        header('Location: register.php');
        exit;
    }

    // パスワード文字数チェック
    if (strlen($password) < 8) {
        $_SESSION['register_error'] =
            'パスワードは8文字以上で入力してください。';

        $_SESSION['register_user_name'] = $user_name;

        header('Location: register.php');
        exit;
    }

    // パスワード文字チェック
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $password)) {
        $_SESSION['register_error'] =
            'パスワードは8文字以上で、半角英数字とアンダースコア（_）のみ使用できます。';

        $_SESSION['register_user_name'] = $user_name;

        header('Location: register.php');
        exit;
    }

    // パスワード確認
    if ($password !== $password_confirmation) {
        $_SESSION['register_error'] =
            'パスワードとパスワード確認が一致しません。';

        $_SESSION['register_user_name'] = $user_name;

        header('Location: register.php');
        exit;
    }

    // ユーザー名重複確認
    $user = find_user_by_user_name(
        $db,
        $user_name
    );

    if ($user !== false) {
        $_SESSION['register_error'] =
            'そのユーザー名はすでに使用されています。';

        $_SESSION['register_user_name'] = $user_name;

        header('Location: register.php');
        exit;
    }

    // ユーザー登録
    try {
        register_user(
            $db,
            $user_name,
            $password
        );
    } catch (Throwable $e) {
        error_log($e->getMessage());

        $_SESSION['register_error'] =
            'ユーザー登録に失敗しました。';

        $_SESSION['register_user_name'] = $user_name;

        header('Location: register.php');
        exit;
    }

    // 登録成功
    $_SESSION['register_success'] =
        'ユーザー登録が完了しました。ログインしてください。';

    $_SESSION['register_user_name'] = $user_name;

    header('Location: index.php');
    exit;
}

$register_error = $_SESSION['register_error'] ?? '';
unset($_SESSION['register_error']);

$user_name = $_SESSION['register_user_name'] ?? '';
unset($_SESSION['register_user_name']);

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ユーザー登録</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/register.css">
</head>

<body>

<?php require_once __DIR__ . '/header.php'; ?>

<div class="container">

    <h1>ユーザー登録</h1>

    <?php if ($register_error !== ''): ?>
        <div class="register-error">
            <?= htmlspecialchars(
                $register_error,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
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