<?php

// ==============================
// ユーザー情報を取得
// ==============================
function find_user($db, $user_name, $input_password)
{
    $sql = '
        SELECT user_id, admin_flg
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
