<?php

session_set_cookie_params([
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();