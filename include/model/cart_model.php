<?php


// カートの商品を取得
function show_cart($db, $user_id){
    $sql = '
        SELECT
            c.cart_id,
            c.user_id,
            c.product_id,
            c.product_qty,
            p.product_name,
            p.price,
            s.stock_qty,
            i.image_name
        FROM ec_cart c
        INNER JOIN ec_product p
            ON c.product_id = p.product_id
        INNER JOIN ec_stock s
            ON c.product_id = s.product_id
        LEFT JOIN ec_image i
            ON p.product_id = i.product_id
        WHERE c.user_id = ? AND p.public_flg = 1
        ORDER BY c.cart_id ASC
    ';

    $stmt = $db->prepare($sql);
    $stmt->execute([$user_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// カートの商品を削除
function delete_cart($db, $user_id, $cart_id){
    $sql = '
        DELETE FROM ec_cart
        WHERE cart_id = ?
        AND user_id = ?
    ';

    $stmt = $db->prepare($sql);

    return $stmt->execute([
        $cart_id,
        $user_id
    ]);
}


// カートの商品個数を変更
function change_cart_qty($db,$user_id,$cart_id,$product_qty) {
    
    // カートの商品情報を取得
    $sql = '
        SELECT
            c.product_id,
            s.stock_qty
        FROM ec_cart c
        INNER JOIN ec_stock s
            ON c.product_id = s.product_id
        WHERE c.cart_id = ?
        AND c.user_id = ?
    ';

    $stmt = $db->prepare($sql);

    $stmt->execute([
        $cart_id,
        $user_id
    ]);

    $cart = $stmt->fetch(PDO::FETCH_ASSOC);

    // カート商品が存在しない場合
    if ($cart === false) {
        return 'カートの商品が見つかりません。';
    }

    
    // 在庫数を超えていないか確認 
    if ($product_qty > (int)$cart['stock_qty']) {
        return '在庫数を超える個数には変更できません。';
    }

    
    // 個数を変更   
    $sql = '
        UPDATE ec_cart
        SET
            product_qty = ?,
            update_date = NOW()
        WHERE cart_id = ?
        AND user_id = ?
    ';

    $stmt = $db->prepare($sql);

    $result = $stmt->execute([
        $product_qty,
        $cart_id,
        $user_id
    ]);

    if (!$result) {
        return 'カートの商品数の変更に失敗しました。';
    }

    return true;
}



// 在庫を減らす
function reduce_stock($db,$product_id,$product_qty) {
    $sql = '
        UPDATE ec_stock
        SET
            stock_qty = stock_qty - ?,
            update_date = NOW()
        WHERE product_id = ?
        AND stock_qty >= ?
    ';

    $stmt = $db->prepare($sql);

    $stmt->execute([
        $product_qty,
        $product_id,
        $product_qty
    ]);

    return $stmt->rowCount() > 0;
}