<?php

// ==============================
// セッション開始
// ==============================
require_once __DIR__ . '/../../include/common/session.php';

// ==============================
// Model
// ==============================
require_once __DIR__ . '/../../include/model/user_model.php';

// ==============================
// 共通認証処理
// ==============================
require_once __DIR__ . '/../../include/common/auth.php';

// ==============================
// Cookie
// ==============================
require_once __DIR__ . '/../../include/common/cookie.php';

// ==============================
// データベース接続
// ==============================
require_once __DIR__ . '/../../include/common/database.php';
$db = connect_database();

// ==============================
// POST以外のアクセスを拒否
// ==============================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// ==============================
// CSRFチェック
// ==============================
verify_csrf_token();

// ==============================
// POSTデータ取得
// ==============================
$user_name = $_POST['user_name'] ?? '';
$input_password = $_POST['password'] ?? '';
$remember_user_name = $_POST['remember_user_name'] ?? '';

// ==============================
// 入力チェック
// ==============================
if ($user_name === '' || $input_password === '') {

    $_SESSION['login_error'] = 'ユーザー名とパスワードを入力してください。';

    header('Location: index.php');
    exit();
}

// ==============================
// ログイン確認
// ==============================
$user = find_user($db,$user_name,$input_password);

// ==============================
// ログイン失敗
// ==============================
if ($user === false) {
    $_SESSION['login_error'] =
        'ユーザー名とパスワードが一致しません。';
    header('Location: index.php');
    exit();
}

// ==============================
// ログイン成功
// ==============================

// セッションID再生成（セッション固定攻撃への対策のため、
// セッションIDをログイン前とログイン後に変える）
session_regenerate_id(true);

// セッション保存
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['user_name'] = $user['user_name'];
$_SESSION['admin_flg'] = (int)$user['admin_flg'];

// ==============================
// Cookie処理
// ==============================
if ($remember_user_name === 'checked') {
    save_login_cookie($user['user_name']);
} else {
    delete_login_cookie();
}

// ==============================
// 権限によって遷移
// ==============================
if ($_SESSION['admin_flg'] === 1) {
    header('Location: admin_products.php');
    exit();
}

header('Location: products.php');
exit();
