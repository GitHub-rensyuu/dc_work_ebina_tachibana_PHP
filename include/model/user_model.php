<?php

// ==============================
// ユーザー情報を取得
// ==============================
function find_user($db, $user_name, $input_password)
{
    $sql = '
        SELECT user_id, user_name, admin_flg
        FROM ec_user
        WHERE user_name = ?
        AND password = ?
    ';

    $stmt = $db->prepare($sql);

    if ($stmt === false) {
        throw new Exception(
            'SQLエラー：SQL文を準備できませんでした。'
        );
    }

    $stmt->execute([
        $user_name,
        $input_password
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// ==============================
// ユーザー名からユーザー情報を取得
// ==============================
function find_user_by_user_name($db, $user_name)
{
    $sql = '
        SELECT user_id
        FROM ec_user
        WHERE user_name = ?
    ';

    $stmt = $db->prepare($sql);

    if ($stmt === false) {
        throw new Exception(
            'SQLエラー：SQL文を準備できませんでした。'
        );
    }

    $stmt->execute([
        $user_name
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}


// ==============================
// ユーザー登録
// ==============================
function register_user($db, $user_name, $password)
{
    $sql = '
        INSERT INTO ec_user (
            user_name,
            password,
            create_date,
            update_date,
            admin_flg
        )
        VALUES (
            ?,
            ?,
            NOW(),
            NOW(),
            0
        )
    ';

    $stmt = $db->prepare($sql);

    if ($stmt === false) {
        throw new Exception(
            'SQLエラー：SQL文を準備できませんでした。'
        );
    }

    return $stmt->execute([
        $user_name,
        $password
    ]);
}
