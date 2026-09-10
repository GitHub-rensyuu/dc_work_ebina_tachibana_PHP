<?php

    function validate_product_post($product_name,$price,$stock_qty,$public_flg,$file){
        $product_name = trim($product_name);
        if (empty($product_name)) {
            return '商品名を入力してください。';
        }
        if (mb_strlen($product_name) > 50) {
            return '商品名は50文字以内で入力してください。';
        }
        if ($price === '' || filter_var($price, FILTER_VALIDATE_INT) === false || $price < 0) {
            return '正しい価格を入力してください。';
        }
        if ($stock_qty === '' || filter_var($stock_qty, FILTER_VALIDATE_INT) === false || $stock_qty < 0) {
            return '正しい個数を入力してください。';
        }

        if (!in_array((int)$public_flg, [0, 1], true)) {
            return '公開ステータスが正しくありません。';
        }
        if (
            !isset($file) ||
            $file['error'] !== UPLOAD_ERR_OK
        ) {
            return '画像ファイルを選択してください。';
        }
        $type = mime_content_type($file['tmp_name']);
        if (!in_array($type, ['image/jpeg', 'image/png'], true)) {
            return 'ファイルの形式が正しくありません（jpgまたはpng形式の画像のみアップロードできます。）';
        }
        return '';
    }

    // 商品テーブルへの保存
    function insert_product($db, $product_name, $price, $public_flg){
        $stmt = $db->prepare(
            "INSERT INTO ec_product(product_name, price, public_flg, create_date, update_date)
            VALUES (?, ?, ?, NOW(), NOW())"
        );

        return $stmt->execute([$product_name, $price, $public_flg]);
    }

    // 在庫テーブルへの保存
    function insert_stock($db,$product_id,$stock_qty){
        $stmt = $db->prepare(
            "INSERT INTO ec_stock(product_id, stock_qty, create_date, update_date)
            VALUES (?, ?, NOW(), NOW())"
        );
        return $stmt->execute([$product_id, $stock_qty]);
    }

    // 画像テーブルへの保存
    function insert_image($db,$product_id,$image_name){
        $stmt = $db->prepare(
            "INSERT INTO ec_image(product_id, image_name, create_date, update_date)
            VALUES (?, ?, NOW(), NOW())"
        );
        return $stmt->execute([$product_id, $image_name]);
    }

    //商品登録
    function register_product(
        $db,
        $product_name,
        $price,
        $stock_qty,
        $public_flg,
        $file
    ) {
        $db->beginTransaction();
        $save = null;
        try {
            // ① 商品登録
            if (!insert_product($db, $product_name, $price, $public_flg)) {
                throw new Exception('商品登録に失敗しました。');
            }
            // ② product_id取得
            $product_id = $db->lastInsertId();
            // ③ 在庫登録
            if (!insert_stock($db, $product_id, $stock_qty)) {
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

            // ロールバック時に削除するため保存
            $save = $image_path;

            // 画像を保存	
            if (!move_uploaded_file($file['tmp_name'], $image_path)) {	
            throw new Exception('画像の保存に失敗しました。');	
            }

            // ⑤ 画像テーブル登録
            if (!insert_image($db, $product_id, $image_name)) {
                throw new Exception('画像情報の登録に失敗しました。');
            }
            // 全部成功
            $db->commit();
            return ['商品を登録しました。',''];

        } catch (Throwable $e) {
            // DBをロールバック
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            // 保存済み画像があれば削除
            if ($save !== null && file_exists($save)) {	
                unlink($save);	
            }

            return [	'',	$e->getMessage()];

        }
    }

    function update_public($db, $product_id){
        $stmt = $db->prepare(
            "SELECT public_flg
            FROM ec_product
            WHERE product_id = ?"
        );

        $stmt->execute([$product_id]);

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            return '商品が見つかりません。';
        }

        $new_flg = ($product['public_flg'] == 1) ? 0 : 1;

        $stmt = $db->prepare(
            "UPDATE ec_product
            SET public_flg = ?, update_date = NOW()
            WHERE product_id = ?"
        );

        $stmt->execute([$new_flg,$product_id]);

        return ($new_flg == 1)
            ? '公開しました。'
            : '非公開にしました。';
    }

    // 在庫数変更
    function update_stock($db, $product_id, $stock_qty){

        // 商品が存在するか確認
        $stmt = $db->prepare(
            "SELECT product_id
            FROM ec_product
            WHERE product_id = ?"
        );

        $stmt->execute([$product_id]);

        if (!$stmt->fetch()) {
            return '商品が見つかりません。';
        }

        // 在庫数を変更
        $stmt = $db->prepare(
            "UPDATE ec_stock
            SET stock_qty = ?, update_date = NOW()
            WHERE product_id = ?"
        );

        $stmt->execute([
            $stock_qty,
            $product_id
        ]);

        return '在庫数を変更しました。';
    }

    // 価格変更
    function update_price($db, $product_id, $price){

        // 商品が存在するか確認
        $stmt = $db->prepare(
            "SELECT product_id
            FROM ec_product
            WHERE product_id = ?"
        );

        $stmt->execute([$product_id]);

        if (!$stmt->fetch()) {
            return '商品が見つかりません。';
        }

        // 価格を変更
        $stmt = $db->prepare(
            "UPDATE ec_product
            SET price = ?, update_date = NOW()
            WHERE product_id = ?"
        );

        $stmt->execute([$price,$product_id]);

        return '価格を変更しました。';
    }


  // 商品削除
  function delete_product($db, $product_id){

    // 商品に紐づく画像ファイル名を取得
    $stmt = $db->prepare(
        "SELECT image_name
         FROM ec_image
         WHERE product_id = ?"
    );

    $stmt->execute([$product_id]);

    $image = $stmt->fetch(PDO::FETCH_ASSOC);

    $db->beginTransaction();

    try {

        // ① 画像テーブルから削除
        $stmt = $db->prepare(
            "DELETE FROM ec_image
             WHERE product_id = ?"
        );

        if (!$stmt->execute([$product_id])) {
            throw new Exception('画像情報の削除に失敗しました。');
        }

        // ② 在庫テーブルから削除
        $stmt = $db->prepare(
            "DELETE FROM ec_stock
             WHERE product_id = ?"
        );

        if (!$stmt->execute([$product_id])) {
            throw new Exception('在庫情報の削除に失敗しました。');
        }

        // ③ 商品テーブルから削除
        $stmt = $db->prepare(
            "DELETE FROM ec_product
             WHERE product_id = ?"
        );

        if (!$stmt->execute([$product_id])) {
            throw new Exception('商品情報の削除に失敗しました。');
        }

        // DBの削除が成功
        $db->commit();

        // ④ 画像ファイルを削除
        if ($image && !empty($image['image_name'])) {
            $image_dir = __DIR__ . '/../../htdocs/ec_site/img';
            $image_path = $image_dir . '/' . $image['image_name'];

            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        return '商品を削除しました。';

    } catch (Throwable $e) {

        // DBをロールバック
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        return $e->getMessage();
    }
  }

?>