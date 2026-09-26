<?php

require_once __DIR__ . '/../config/const.php';

session_set_cookie_params([
    'secure' => SESSION_COOKIE_SECURE,
    'httponly' => SESSION_COOKIE_HTTPONLY,
    'samesite' => SESSION_COOKIE_SAMESITE
]);

session_start();
