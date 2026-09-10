<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>カート</title>
    <link rel="stylesheet" href="css/header.css">

    <style>
        table {
            border: solid black 1px;
            width: 800px;
            border-collapse: collapse;
            margin: 0 auto;
        }

        th,
        td {
            border: 1px solid #bbb;
            padding: 10px;
        }

        th {
            background-color: #eee;
        }

        tbody tr {
            height: 100px;
        }

        /* 商品セル */
        .product {
            height: 100px;
            vertical-align: middle;
        }

        /* 商品の中身 */
        .product-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* 商品画像 */
        .product img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            flex-shrink: 0;
        }

        /* 商品名 */
        .product-name {
            font-size: 16px;
        }

        /* 画像なし */
        .no-image {
            width: 80px;
            text-align: center;
            color: #888;
        }

        /* 削除ボタン */
        td:nth-child(2) {
            text-align: center;
        }

        /* 個数フォーム */
        .qty-form {
            display: flex;
            align-items: center;
            gap: 3px;
            justify-content: center;
        }

        /* 個数入力欄 */
        .qty-form input[type="number"] {
            width: 35px;
            padding: 3px;
            box-sizing: border-box;
        }

        /* 変更ボタン */
        .qty-form input[type="submit"] {
            padding: 3px 5px;
            font-size: 12px;
            white-space: nowrap;
        }

        /* 個数の列 */
        .qty-form {
            width: max-content;
            margin: 0 auto;
        }

        /* 空のカート */
        .empty-cart {
            margin-top: 20px;
            text-align: center;
            color: red;
            font-weight: bold;
        }

        .cart-error {
            width: 800px;
            margin: 20px auto;
            text-align: center;
            color: red;
            font-weight: bold;
            font-size: 18px;
        }

        /* 合計金額 */
        .total {
            width: 800px;
            text-align: right;
            margin: 20px auto 0;
            font-size: 20px;
            font-weight: bold;
            color: red;
        }

        .purchase-form {
            width: 800px;
            margin: 20px auto 0;
            text-align: right;
        }

        .purchase-form button {
            padding: 10px 30px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        /* 商品一覧へ戻る */
        .cart-bottom {
            width: 800px;
            margin: 10px auto 0;
            text-align: left;
        }

        /* タイトル */
        h1 {
            text-align: center;
        }
    </style>
</head>

<body>
    <?php
    $show_header_menu = true;
    require_once __DIR__ . '/../../htdocs/ec_site/header.php';
    ?>

    <h1>カート</h1>

    <?php if (!empty($cart_error)): ?>

        <p class="cart-error">
            <?= htmlspecialchars(
                $cart_error,
                ENT_QUOTES,
                'UTF-8'
            ) ?>
        </p>

    <?php endif; ?>

    <?php if (empty($cart_items)): ?>

        <p class="empty-cart">
            カートに商品が入っていません。
        </p>

    <?php else: ?>

        <table>

            <thead>
                <tr>
                    <th>商品</th>
                    <th></th>
                    <th>価格</th>
                    <th>個数</th>
                    <th>小計</th>
                </tr>
            </thead>

            <tbody>

                <?php
                $total = 0;
                ?>

                <?php foreach ($cart_items as $item): ?>

                    <?php
                    $subtotal =
                        (int)$item['price'] *
                        (int)$item['product_qty'];

                    $total += $subtotal;
                    ?>

                    <tr>

                        <!-- 商品 -->
                        <td class="product">
                            <div class="product-content">

                                <?php if (!empty($item['image_name'])): ?>

                                    <img
                                        src="img/<?= htmlspecialchars(
                                            $item['image_name'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                        alt="<?= htmlspecialchars(
                                            $item['product_name'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                                    >

                                <?php else: ?>
                            </div>
                                <span class="no-image">画像なし</span>

                            <?php endif; ?>

                            <span class="product-name">
                                <?= htmlspecialchars(
                                    $item['product_name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </span>

                        </td>

                        <!-- 削除 -->
                        <td>
                            <form action="cart.php" method="post">
                                <input
                                    type="hidden"
                                    name="cart_id"
                                    value="<?= htmlspecialchars(
                                        $item['cart_id'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                >

                                <button type="submit" name="delete_cart">
                                    削除する
                                </button>
                            </form>
                        </td>

                        <!-- 価格 -->
                        <td>
                            ¥ <?= number_format((int)$item['price']) ?>
                        </td>

                        <!-- 個数 -->
                        <td>
                            <form action="cart.php" method="post" class="qty-form">

                                <input
                                    type="hidden"
                                    name="cart_id"
                                    value="<?= htmlspecialchars(
                                        $item['cart_id'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                >

                                <input
                                    type="number"
                                    name="product_qty"
                                    value="<?= htmlspecialchars(
                                        $item['product_qty'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                    min="1"
                                >

                                <input
                                    type="submit"
                                    name="change_qty"
                                    value="変更する"
                                >

                            </form>
                        </td>

                        <!-- 小計 -->
                        <td>
                            ¥ <?= number_format($subtotal) ?>
                        </td>

                    </tr>


                <?php endforeach; ?>

            </tbody>

        </table>

        <div class="total">
            合計金額：
            ¥ <?= number_format($total) ?>
        </div>

        <form action="cart.php" method="post" class="purchase-form">
            <button type="submit" name="purchase">
                購入する
            </button>
        </form>

        
    <?php endif; ?>
    <div class="cart-bottom">
        <a href="products.php">商品一覧へ戻る</a>
    </div>

</body>

</html>
