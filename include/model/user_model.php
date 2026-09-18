<?php

// ==============================
// ユーザー名からユーザー情報を取得
// ==============================
function find_user_by_user_name($db, $user_name)
{
    $sql = '
        SELECT
            user_id,
            user_name,
            password,
            admin_flg
        FROM ec_user
        WHERE user_name = ?
    ';

    $stmt = $db->prepare($sql);

    if ($stmt === false) {
        throw new Exception(
            'SQLエラー：SQL文を準備できませんでした。'
        );
    }

    $stmt->execute([$user_name]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}


// ==============================
// ログイン確認
// ==============================
function find_user($db, $user_name, $input_password)
{
    $user = find_user_by_user_name($db, $user_name);

    // ユーザーが存在しない
    if ($user === false) {
        return false;
    }

    // パスワードを検証
    if (!password_verify($input_password, $user['password'])) {
        return false;
    }

    return $user;
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

    // パスワードをハッシュ化
    $hashed_password = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    return $stmt->execute([
        $user_name,
        $hashed_password
    ]);
}
