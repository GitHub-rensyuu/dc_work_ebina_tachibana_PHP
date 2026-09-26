<?php

require_once __DIR__ . '/../config/const.php';


// ログイン済みか確認
function require_login(){
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . PAGE_INDEX);
        exit;
    }
}


// 一般ユーザーか確認
function require_user(){
    require_login();

    if (
        isset($_SESSION['admin_flg']) &&
        (int)$_SESSION['admin_flg'] === ADMIN_FLG_ADMIN
    ) {
        header('Location: ' . PAGE_ADMIN_PRODUCTS);
        exit;
    }
}


// 管理者か確認
function require_admin(){
    require_login();

    if (
        !isset($_SESSION['admin_flg']) ||
        (int)$_SESSION['admin_flg'] !== ADMIN_FLG_ADMIN
    ) {
        header('Location: ' . PAGE_PRODUCTS);
        exit;
    }
}


// ログアウト処理
function logout(){
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            [
                'expires' => time() - 3600,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite']
            ]
        );
    }

    session_destroy();

    header('Location: ' . PAGE_INDEX);
    exit;
}


// CSRFトークンを取得・生成
function get_csrf_token(){
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(
            random_bytes(32)
        );
    }

    return $_SESSION['csrf_token'];
}


// CSRFトークンを確認
function verify_csrf_token(){
    if (
        !isset($_POST['csrf_token']) ||
        !isset($_SESSION['csrf_token']) ||
        !is_string($_POST['csrf_token']) ||
        !is_string($_SESSION['csrf_token']) ||
        !hash_equals(
            $_SESSION['csrf_token'],
            $_POST['csrf_token']
        )
    ) {
        http_response_code(403);
        exit('不正なリクエストです。');
    }
}
