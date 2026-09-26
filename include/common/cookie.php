<?php

require_once __DIR__ . '/../config/const.php';


// ログインユーザー名Cookieを保存
function save_login_cookie($user_name){
    $expiration =
        time() + COOKIE_EXPIRATION_DAYS * 24 * 60 * 60;

    setcookie(
        COOKIE_REMEMBER_USER_NAME,
        COOKIE_VALUE_CHECKED,
        [
            'expires' => $expiration,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]
    );

    setcookie(
        COOKIE_USER_NAME,
        $user_name,
        [
            'expires' => $expiration,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]
    );
}


// ログインユーザー名Cookieを削除
function delete_login_cookie(){
    $expiration = time() - 3600;

    setcookie(
        COOKIE_REMEMBER_USER_NAME,
        '',
        [
            'expires' => $expiration,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]
    );

    setcookie(
        COOKIE_USER_NAME,
        '',
        [
            'expires' => $expiration,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]
    );
}
