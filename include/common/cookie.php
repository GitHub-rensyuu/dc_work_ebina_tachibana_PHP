<?php

define('COOKIE_EXPIRATION_DAYS', 30);

// ==============================
// ログインユーザー名Cookieを保存
// ==============================
function save_login_cookie($user_name)
{
    $expiration =
        time() + COOKIE_EXPIRATION_DAYS * 24 * 60 * 60;

    setcookie(
        'remember_user_name',
        'checked',
        [
            'expires' => $expiration,
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Lax'
        ]
    );

    setcookie(
        'user_name',
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

// ==============================
// ログインユーザー名Cookieを削除
// ==============================
function delete_login_cookie()
{
    $expiration = time() - 3600;

    setcookie(
        'remember_user_name',
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
        'user_name',
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