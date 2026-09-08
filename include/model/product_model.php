<?php

// ==============================
// 商品一覧を取得
// ==============================
function show_products($db)
{
    $stmt = $db->query("
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
        ORDER BY p.product_id ASC
    ");

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
