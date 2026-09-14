<?php
  define('COOKIE_EXPIRATION_DAYS', 30);
  $expiration = time() + COOKIE_EXPIRATION_DAYS * 24 * 60 * 60;

  function save_login_cookie($user_name){
      setcookie(
          'cookie_confirmation',
          'checked',
          $expiration
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

  function delete_login_cookie(){
      setcookie(
          'cookie_confirmation',
          '',
          time() - 3600
      );

      setcookie(
          'user_name',
          '',
          time() - 3600
      );
  }
?>