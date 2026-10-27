<?php

require_once __DIR__ . '/../../include/common/session.php';
require_once __DIR__ . '/../../include/common/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

verify_csrf_token();
logout();
