<?php
  
  function connect_database(){
    // データベース接続情報
    $host = 'localhost';
    $login_user = 'xb513874_h8646';
    $password = '1r86160zfh';
    $database = 'xb513874_g1gw7';
  
    // データベースへ接続、文字コード設定
    try {
        $db = new PDO(
            "mysql:host=$host;dbname=$database;charset=utf8mb4",
            $login_user,
            $password
        );

        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        error_log($e->getMessage());
        die('データベース接続に失敗しました。');
    }
  }

?>