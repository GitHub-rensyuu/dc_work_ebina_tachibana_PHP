<?php

require_once __DIR__ . '/../config/const.php';


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

    if (mb_strlen($product_name) > PRODUCT_NAME_MAX_LENGTH) {
        return '商品名は' .
            PRODUCT_NAME_MAX_LENGTH .
            '文字以内で入力してください。';
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
        !in_array(
            (int)$public_flg,
            [
                PRODUCT_PUBLIC_FLG_PRIVATE,
                PRODUCT_PUBLIC_FLG_PUBLIC
            ],
            true
        )
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

    if (
        !in_array(
            $type,
            [
                PRODUCT_IMAGE_MIME_JPEG,
                PRODUCT_IMAGE_MIME_PNG
            ],
            true
        )
    ) {
        return 'ファイルの形式が正しくありません（jpgまたはpng形式の画像のみアップロードできます。）。';
    }

    return '';
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

        if (!insert_product(
            $db,
            $product_name,
            $price,
            $public_flg
        )) {
            throw new Exception('商品登録に失敗しました。');
        }

        $product_id = (int)$db->lastInsertId();

        if (!insert_stock(
            $db,
            $product_id,
            $stock_qty
        )) {
            throw new Exception('在庫登録に失敗しました。');
        }

        $type = mime_content_type($file['tmp_name']);

        if ($type === PRODUCT_IMAGE_MIME_JPEG) {
            $extension = PRODUCT_IMAGE_EXTENSION_JPEG;
        } elseif ($type === PRODUCT_IMAGE_MIME_PNG) {
            $extension = PRODUCT_IMAGE_EXTENSION_PNG;
        } else {
            throw new Exception('画像形式が正しくありません。');
        }

        $image_dir = PRODUCT_IMAGE_DIR;

        if (
            !is_dir($image_dir) &&
            !mkdir(
                $image_dir,
                PRODUCT_IMAGE_DIRECTORY_PERMISSION,
                true
            )
        ) {
            throw new Exception(
                '画像保存フォルダの作成に失敗しました。'
            );
        }

        $image_name =
            uniqid('', true) . '.' . $extension;

        $image_path =
            $image_dir . '/' . $image_name;

        if (!move_uploaded_file(
            $file['tmp_name'],
            $image_path
        )) {
            throw new Exception(
                '画像の保存に失敗しました。'
            );
        }

        if (!insert_image(
            $db,
            $product_id,
            $image_name
        )) {
            throw new Exception(
                '画像情報の登録に失敗しました。'
            );
        }

        $db->commit();

        return [
            '商品を登録しました。',
            ''
        ];
    } catch (Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        if (
            $image_path !== null &&
            file_exists($image_path)
        ) {
            if (!unlink($image_path)) {
                error_log(
                    '商品画像の削除に失敗しました: ' .
                    $image_path
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

// 商品情報を登録
function insert_product($db,$product_name,$price,$public_flg) {
    $sql = '
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
    ';

    $stmt = $db->prepare($sql);

    return $stmt->execute([
        $product_name,
        $price,
        $public_flg
    ]);
}


// 在庫情報を登録
function insert_stock($db,$product_id,$stock_qty) {
    $sql = '
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
    ';

    $stmt = $db->prepare($sql);

    return $stmt->execute([
        $product_id,
        $stock_qty
    ]);
}


// 商品画像情報を登録
function insert_image($db,$product_id,$image_name) {
    $sql = '
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
    ';

    $stmt = $db->prepare($sql);

    return $stmt->execute([
        $product_id,
        $image_name
    ]);
}

// 商品の在庫数を変更
function update_stock(
    $db,
    $product_id,
    $stock_qty
) {
    try {
        $sql = '
            UPDATE ec_stock
            SET
                stock_qty = ?,
                update_date = NOW()
            WHERE product_id = ?
        ';

        $stmt = $db->prepare($sql);

        $stmt->execute([
            $stock_qty,
            $product_id
        ]);

        if ($stmt->rowCount() === 0) {
            return '在庫数の変更がありませんでした。';
        }

        return '在庫数を変更しました。';

    } catch (Throwable $e) {
        error_log($e->getMessage());

        return '在庫数の変更に失敗しました。';
    }
}


// 商品の価格を変更
function update_price(
    $db,
    $product_id,
    $price
) {
    try {
        $sql = '
            UPDATE ec_product
            SET
                price = ?,
                update_date = NOW()
            WHERE product_id = ?
        ';

        $stmt = $db->prepare($sql);

        $stmt->execute([
            $price,
            $product_id
        ]);

        if ($stmt->rowCount() === 0) {
            return '価格の変更がありませんでした。';
        }

        return '価格を変更しました。';

    } catch (Throwable $e) {
        error_log($e->getMessage());

        return '価格の変更に失敗しました。';
    }
}


// 商品の公開・非公開を切り替える
function update_public(
    $db,
    $product_id
) {
    try {
        // 現在の公開状態を取得
        $sql = '
            SELECT public_flg
            FROM ec_product
            WHERE product_id = ?
        ';

        $stmt = $db->prepare($sql);

        $stmt->execute([
            $product_id
        ]);

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product === false) {
            return '商品が見つかりません。';
        }

        // 公開なら非公開、非公開なら公開
        if (
            (int)$product['public_flg'] ===
            PRODUCT_PUBLIC_FLG_PUBLIC
        ) {
            $new_public_flg =
                PRODUCT_PUBLIC_FLG_PRIVATE;

            $message = '商品を非公開にしました。';
        } else {
            $new_public_flg =
                PRODUCT_PUBLIC_FLG_PUBLIC;

            $message = '商品を公開しました。';
        }

        // 公開状態を更新
        $sql = '
            UPDATE ec_product
            SET
                public_flg = ?,
                update_date = NOW()
            WHERE product_id = ?
        ';

        $stmt = $db->prepare($sql);

        $stmt->execute([
            $new_public_flg,
            $product_id
        ]);

        return $message;

    } catch (Throwable $e) {
        error_log($e->getMessage());

        return '公開状態の変更に失敗しました。';
    }
}


// 商品を削除
function delete_product(
    $db,
    $product_id
) {
    $image_paths = [];

    try {
        $db->beginTransaction();

        // 商品が存在するか確認
        $sql = '
            SELECT product_id
            FROM ec_product
            WHERE product_id = ?
        ';

        $stmt = $db->prepare($sql);

        $stmt->execute([
            $product_id
        ]);

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product === false) {
            $db->rollBack();

            return '商品が見つかりません。';
        }


        // 商品画像を取得
        $sql = '
            SELECT image_name
            FROM ec_image
            WHERE product_id = ?
        ';

        $stmt = $db->prepare($sql);

        $stmt->execute([
            $product_id
        ]);

        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);


        // 削除対象の画像ファイルパスを作成
        foreach ($images as $image) {
            if (
                !empty($image['image_name']) &&
                is_string($image['image_name'])
            ) {
                $image_paths[] =
                    PRODUCT_IMAGE_DIR .
                    '/' .
                    basename($image['image_name']);
            }
        }


        // 商品画像情報を削除
        $sql = '
            DELETE FROM ec_image
            WHERE product_id = ?
        ';

        $stmt = $db->prepare($sql);

        $stmt->execute([
            $product_id
        ]);


        // 在庫情報を削除
        $sql = '
            DELETE FROM ec_stock
            WHERE product_id = ?
        ';

        $stmt = $db->prepare($sql);

        $stmt->execute([
            $product_id
        ]);


        // カートの商品を削除
        $sql = '
            DELETE FROM ec_cart
            WHERE product_id = ?
        ';

        $stmt = $db->prepare($sql);

        $stmt->execute([
            $product_id
        ]);


        // 商品情報を削除
        $sql = '
            DELETE FROM ec_product
            WHERE product_id = ?
        ';

        $stmt = $db->prepare($sql);

        $stmt->execute([
            $product_id
        ]);


        // DBの削除を確定
        $db->commit();


        // DB削除成功後に画像ファイルを削除
        foreach ($image_paths as $image_path) {
            if (
                file_exists($image_path) &&
                !unlink($image_path)
            ) {
                error_log(
                    '商品画像の削除に失敗しました: ' .
                    $image_path
                );
            }
        }


        return '商品を削除しました。';

    } catch (Throwable $e) {

        if ($db->inTransaction()) {
            $db->rollBack();
        }

        error_log($e->getMessage());

        return '商品の削除に失敗しました。';
    }
}



