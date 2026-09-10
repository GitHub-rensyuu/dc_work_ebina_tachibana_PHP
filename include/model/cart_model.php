<?php

// ==============================
// カートの商品を取得
// ==============================
function show_cart($db, $user_id)
{
    $stmt = $db->prepare(
        "SELECT
            c.cart_id,
            c.user_id,
            c.product_id,
            c.product_qty,
            p.product_name,
            p.price,
            i.image_name
         FROM ec_cart c
         INNER JOIN ec_product p
             ON c.product_id = p.product_id
         LEFT JOIN ec_image i
             ON p.product_id = i.product_id
         WHERE c.user_id = ?
         ORDER BY c.cart_id ASC"
    );

    $stmt->execute([$user_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ==============================
// カートの商品を削除
// ==============================
function delete_cart($db, $user_id, $cart_id)
{
    $stmt = $db->prepare(
        "DELETE FROM ec_cart
         WHERE cart_id = ?
         AND user_id = ?"
    );

    return $stmt->execute([
        $cart_id,
        $user_id
    ]);
}

// ==============================
// カートの商品個数を変更
// ==============================
function change_cart_qty($db, $user_id, $cart_id, $product_qty)
{
    $stmt = $db->prepare(
        "UPDATE ec_cart
         SET product_qty = ?
         WHERE cart_id = ?
         AND user_id = ?"
    );

    return $stmt->execute([
        $product_qty,
        $cart_id,
        $user_id
    ]);
}

// ==============================
// 購入時に在庫数を減らす
// ==============================
function reduce_stock($db, $product_id, $product_qty)
{
    $stmt = $db->prepare(
        "UPDATE ec_stock
         SET stock_qty = stock_qty - ?
         WHERE product_id = ?
         AND stock_qty >= ?"
    );

    $stmt->execute([
        $product_qty,
        $product_id,
        $product_qty
    ]);

    // 更新された場合は1、更新されなかった場合は0
    return $stmt->rowCount() > 0;
}
