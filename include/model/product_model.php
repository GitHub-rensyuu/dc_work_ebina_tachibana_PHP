<?php

// 商品一覧を取得
function show_products($db, $search = '', $sort = ''){
    $sql = '
        SELECT
            p.product_id,
            p.product_name,
            p.price,
            p.public_flg,
            s.stock_qty,
            i.image_name
        FROM ec_product p
        LEFT JOIN ec_stock s
            ON p.product_id = s.product_id
        LEFT JOIN ec_image i
            ON p.product_id = i.product_id
        WHERE p.product_name LIKE ?
    ';

    if ($sort === 'price_asc') {
        $sql .= ' ORDER BY p.price ASC';
    } elseif ($sort === 'price_desc') {
        $sql .= ' ORDER BY p.price DESC';
    } elseif ($sort === 'newest') {
        $sql .= ' ORDER BY p.product_id DESC';
    } else {
        $sql .= ' ORDER BY p.product_id ASC';
    }

    $stmt = $db->prepare($sql);

    $stmt->execute([
        '%' . $search . '%'
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



// カートに商品を追加
function add_cart($db, $user_id, $product_id){

    // 商品が存在するか確認   
    $sql = '
        SELECT
            product_id,
            public_flg,
            price
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

    
    // 非公開の商品は追加できない   
    if ((int)$product['public_flg'] !== 1) {
        return 'この商品は現在購入できません。';
    }

    
    // 在庫確認    
    $sql = '
        SELECT
            stock_qty
        FROM ec_stock
        WHERE product_id = ?
    ';

    $stmt = $db->prepare($sql);

    $stmt->execute([
        $product_id
    ]);

    $stock = $stmt->fetch(PDO::FETCH_ASSOC);

    if (
        $stock === false ||
        (int)$stock['stock_qty'] <= 0
    ) {
        return 'この商品は売り切れです。';
    }


    
    // すでにカートに入っているか確認    
    $stmt = $db->prepare(
        'SELECT
            cart_id,
            product_qty
         FROM ec_cart
         WHERE user_id = ?
         AND product_id = ?'
    );

    $stmt->execute([
        $user_id,
        $product_id
    ]);

    $cart = $stmt->fetch(PDO::FETCH_ASSOC);

    
    // すでにカートにある場合    
    if ($cart !== false) {

        $new_qty = (int)$cart['product_qty'] + 1;

        // 在庫数を超えないようにする
        if ($new_qty > (int)$stock['stock_qty']) {
            return '在庫数を超えてカートに追加することはできません。';
        }

        $stmt = $db->prepare(
            'UPDATE ec_cart
             SET product_qty = ?,
                 update_date = NOW()
             WHERE cart_id = ?'
        );

        $stmt->execute([
            $new_qty,
            $cart['cart_id']
        ]);

        return 'カートの商品数を1個増やしました。';
    }

    
    // カートにない場合  
    $stmt = $db->prepare(
        'INSERT INTO ec_cart(
            user_id,
            product_id,
            product_qty,
            create_date,
            update_date
        )
        VALUES (?, ?, ?, NOW(), NOW())'
    );

    $stmt->execute([
        $user_id,
        $product_id,
        1
    ]);

    return 'カートに商品を追加しました。';
}