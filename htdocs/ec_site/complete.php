<?php

require_once __DIR__ . '/../../include/common/session.php';
require_once __DIR__ . '/../../include/common/auth.php';

require_login();

$purchase_items =
    $_SESSION['purchase_items'] ?? [];

unset($_SESSION['purchase_items']);

include_once __DIR__ . '/../../include/view/complete_view.php';