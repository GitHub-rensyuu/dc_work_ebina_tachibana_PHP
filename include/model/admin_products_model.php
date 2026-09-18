<?php

// 商品登録時の入力チェック
function validate_product_post(
    $product_name,
    $price,
    $stock_qty,
    $public_flg,
    $file
) {
    $product_name = trim($product_name);

    if ($product_name === '') {
        return '商品名を入力してください。';
    }

    if (mb_strlen($product_name) > 50) {
        return '商品名は50文字以内で入力してください。';
    }

    if (
        $price === '' ||
        filter_var($price, FILTER_VALIDATE_INT) === false ||
        (int)$price < 0
    ) {
        return '正しい価格を入力してください。';
    }

    if (
        $stock_qty === '' ||
        filter_var($stock_qty, FILTER_VALIDATE_INT) === false ||
        (int)$stock_qty < 0
    ) {
        return '正しい個数を入力してください。';
    }

    if (
        filter_var($public_flg, FILTER_VALIDATE_INT) === false ||
        !in_array((int)$public_flg, [0, 1], true)
    ) {
        return '公開ステータスが正しくありません。';
    }

    if (
        !is_array($file) ||
        !isset($file['error']) ||
        $file['error'] !== UPLOAD_ERR_OK
    ) {
        return '画像ファイルを選択してください。';
    }

    if (
        !isset($file['tmp_name']) ||
        !is_uploaded_file($file['tmp_name'])
    ) {
        return '画像ファイルが正しくありません。';
    }

    $type = mime_content_type($file['tmp_name']);

    if (!in_array($type, ['image/jpeg', 'image/png'], true)) {
        return 'ファイルの形式が正しくありません（jpgまたはpng形式の画像のみアップロードできます。）。';
    }

    return '';
}


// 商品テーブルへの保存
function insert_product(
    $db,
    $product_name,
    $price,
    $public_flg
) {
    $sql = "
        INSERT INTO ec_product (
            product_name,
            price,
            public_flg,
            create_date,
            update_date
        )
        VALUES (
            ?,
            ?,
            ?,
            NOW(),
            NOW()
        )
    ";

    $stmt = $db->prepare($sql);

    return $stmt->execute([
        $product_name,
        $price,
        $public_flg
    ]);
}


// 在庫テーブルへの保存
function insert_stock(
    $db,
    $product_id,
    $stock_qty
) {
    $sql = "
        INSERT INTO ec_stock (
            product_id,
            stock_qty,
            create_date,
            update_date
        )
        VALUES (
            ?,
            ?,
            NOW(),
            NOW()
        )
    ";

    $stmt = $db->prepare($sql);

    return $stmt->execute([
        $product_id,
        $stock_qty
    ]);
}


// 画像テーブルへの保存
function insert_image(
    $db,
    $product_id,
    $image_name
) {
    $sql = "
        INSERT INTO ec_image (
            product_id,
            image_name,
            create_date,
            update_date
        )
        VALUES (
            ?,
            ?,
            NOW(),
            NOW()
        )
    ";

    $stmt = $db->prepare($sql);

    return $stmt->execute([
        $product_id,
        $image_name
    ]);
}


// 商品登録
function register_product(
    $db,
    $product_name,
    $price,
    $stock_qty,
    $public_flg,
    $file
) {
    $image_path = null;

    try {
        $db->beginTransaction();

        // ① 商品登録
        if (!insert_product(
            $db,
            $product_name,
            $price,
            $public_flg
        )) {
            throw new Exception('商品登録に失敗しました。');
        }

        // ② product_id取得
        $product_id = (int)$db->lastInsertId();

        // ③ 在庫登録
        if (!insert_stock(
            $db,
            $product_id,
            $stock_qty
        )) {
            throw new Exception('在庫登録に失敗しました。');
        }

        // ④ 画像保存
        $type = mime_content_type($file['tmp_name']);

        if ($type === 'image/jpeg') {
            $extension = 'jpg';
        } elseif ($type === 'image/png') {
            $extension = 'png';
        } else {
            throw new Exception('画像形式が正しくありません。');
        }

        // 画像保存先
        $image_dir = __DIR__ . '/../../htdocs/ec_site/img';

        // imgフォルダが無ければ作成
        if (!is_dir($image_dir) && !mkdir($image_dir, 0755, true)) {
            throw new Exception('画像保存フォルダの作成に失敗しました。');
        }

        // ファイル名を作成
        $image_name = uniqid('', true) . '.' . $extension;
        $image_path = $image_dir . '/' . $image_name;

        // 画像を保存
        if (!move_uploaded_file(
            $file['tmp_name'],
            $image_path
        )) {
            throw new Exception('画像の保存に失敗しました。');
        }

        // ⑤ 画像テーブル登録
        if (!insert_image(
            $db,
            $product_id,
            $image_name
        )) {
            throw new Exception('画像情報の登録に失敗しました。');
        }

        // 全部成功
        $db->commit();

        return [
            '商品を登録しました。',
            ''
        ];
    } catch (Throwable $e) {
        // DBをロールバック
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        // 保存済み画像があれば削除
        if (
            $image_path !== null &&
            file_exists($image_path)
        ) {
            if (!unlink($image_path)) {
                error_log(
                    '商品画像の削除に失敗しました: ' . $image_path
                );
            }
        }

        error_log($e->getMessage());

        return [
            '',
            '商品登録に失敗しました。'
        ];
    }
}


// 公開・非公開の切り替え
function update_public($db, $product_id)
{
    try {
        $sql = "
            SELECT public_flg
            FROM ec_product
            WHERE product_id = ?
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute([$product_id]);

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product === false) {
            return '商品が見つかりません。';
        }

        $new_flg = ((int)$product['public_flg'] === 1) ? 0 : 1;

        $sql = "
            UPDATE ec_product
            SET
                public_flg = ?,
                update_date = NOW()
            WHERE product_id = ?
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            $new_flg,
            $product_id
        ]);

        if ($new_flg === 1) {
            return '公開しました。';
        }

        return '非公開にしました。';
    } catch (Throwable $e) {
        error_log($e->getMessage());

        return '公開状態の変更に失敗しました。';
    }
}


// 在庫数変更
function update_stock(
    $db,
    $product_id,
    $stock_qty
) {
    try {
        $sql = "
            SELECT product_id
            FROM ec_product
            WHERE product_id = ?
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute([$product_id]);

        if ($stmt->fetch(PDO::FETCH_ASSOC) === false) {
            return '商品が見つかりません。';
        }

        // 在庫数を変更
        $sql = "
            UPDATE ec_stock
            SET
                stock_qty = ?,
                update_date = NOW()
            WHERE product_id = ?
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            $stock_qty,
            $product_id
        ]);

        return '在庫数を変更しました。';
    } catch (Throwable $e) {
        error_log($e->getMessage());

        return '在庫数の変更に失敗しました。';
    }
}


// 価格変更
function update_price(
    $db,
    $product_id,
    $price
) {
    try {
        $sql = "
            SELECT product_id
            FROM ec_product
            WHERE product_id = ?
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute([$product_id]);

        if ($stmt->fetch(PDO::FETCH_ASSOC) === false) {
            return '商品が見つかりません。';
        }

        // 価格を変更
        $sql = "
            UPDATE ec_product
            SET
                price = ?,
                update_date = NOW()
            WHERE product_id = ?
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            $price,
            $product_id
        ]);

        return '価格を変更しました。';
    } catch (Throwable $e) {
        error_log($e->getMessage());

        return '価格の変更に失敗しました。';
    }
}


// 商品削除
function delete_product($db, $product_id)
{
    $image_name = null;

    try {
        $db->beginTransaction();

        // 商品に紐づく画像ファイル名を取得
        $sql = "
            SELECT image_name
            FROM ec_image
            WHERE product_id = ?
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute([$product_id]);

        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($image !== false) {
            $image_name = $image['image_name'];
        }

        // ① 画像テーブルから削除
        $sql = "
            DELETE FROM ec_image
            WHERE product_id = ?
        ";

        $stmt = $db->prepare($sql);

        if (!$stmt->execute([$product_id])) {
            throw new Exception('画像情報の削除に失敗しました。');
        }

        // ② 在庫テーブルから削除
        $sql = "
            DELETE FROM ec_stock
            WHERE product_id = ?
        ";

        $stmt = $db->prepare($sql);

        if (!$stmt->execute([$product_id])) {
            throw new Exception('在庫情報の削除に失敗しました。');
        }

        // ③ カートから削除
        $sql = "
            DELETE FROM ec_cart
            WHERE product_id = ?
        ";

        $stmt = $db->prepare($sql);

        if (!$stmt->execute([$product_id])) {
            throw new Exception('カート情報の削除に失敗しました。');
        }

        // ④ 商品テーブルから削除
        $sql = "
            DELETE FROM ec_product
            WHERE product_id = ?
        ";

        $stmt = $db->prepare($sql);

        if (!$stmt->execute([$product_id])) {
            throw new Exception('商品情報の削除に失敗しました。');
        }

        if ($stmt->rowCount() === 0) {
            $db->rollBack();

            return '商品が見つかりません。';
        }

        // DBの削除が成功
        $db->commit();

        // ⑤ 画像ファイルを削除
        if ($image_name !== null && $image_name !== '') {
            $image_dir = __DIR__ . '/../../htdocs/ec_site/img';
            $image_path = $image_dir . '/' . $image_name;

            if (
                file_exists($image_path) &&
                !unlink($image_path)
            ) {
                error_log(
                    '商品画像の削除に失敗しました: ' . $image_path
                );
            }
        }

        return '商品を削除しました。';
    } catch (Throwable $e) {
        // DBをロールバック
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        error_log($e->getMessage());

        return '商品削除に失敗しました。';
    }
}

?>
