<?php

require_once __DIR__ . '/../config/const.php';

function connect_database(){
    try {
        $db = new PDO(
            'mysql:host=' . DB_HOST .
            ';dbname=' . DB_NAME .
            ';charset=' . DB_CHARSET,
            DB_USER,
            DB_PASSWORD
        );

        $db->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );

        return $db;
    } catch (PDOException $e) {
        error_log($e->getMessage());

        throw new RuntimeException(
            'データベース接続に失敗しました。',
            0,
            $e
        );
    }
}
