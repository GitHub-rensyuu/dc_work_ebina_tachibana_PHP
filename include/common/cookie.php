<?php
  define('COOKIE_EXPIRATION_DAYS', 30);
  $expiration = time() + COOKIE_EXPIRATION_DAYS * 24 * 60 * 60;

  function save_login_cookie($login_id){
      setcookie(
          'cookie_confirmation',
          'checked',
          $expiration
      );

      setcookie(
          'login_id',
          $login_id,
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
          'login_id',
          '',
          time() - 3600
      );
  }
?>