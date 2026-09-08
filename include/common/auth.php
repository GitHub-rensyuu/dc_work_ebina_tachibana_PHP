<?php

// ==============================
// ログイン済みか確認
// ==============================
function require_login()
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php');
        exit();
    }
}

// ==============================
// 管理者か確認
// ==============================
function require_admin(){
    require_login();

    if (
        !isset($_SESSION['admin_flg']) ||
        $_SESSION['admin_flg'] !== 1
    ) {
        header('Location: products.php');
        exit();
    }
}

// ==============================
// ログアウト処理
// ==============================
function logout(){
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 3600,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();

    header('Location: index.php');
    exit();
}

// ==============================
// CSRFトークンを確認
// ==============================
function verify_csrf_token()
{
    if (
        !isset($_POST['csrf_token']) ||
        !isset($_SESSION['csrf_token']) ||
        !hash_equals(
            $_SESSION['csrf_token'],
            $_POST['csrf_token']
        )
    ) {
        exit('不正なリクエストです。');
    }
}