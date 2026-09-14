<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>購入完了</title>
    <link rel="stylesheet" href="css/header.css">

    <style>
        h1 {
            text-align: center;
            color: red;
        }

        .message {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 30px 0;
        }

        table {
            width: 800px;
            margin: 0 auto;
            border-collapse: collapse;
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

        /* 商品 */
        .product {
            height: 100px;
            vertical-align: middle;
        }

        .product-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* 商品画像 */
        .product img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            flex-shrink: 0;
        }

        /* 商品名 */
        .product-name {
            font-size: 16px;
        }

        /* 合計金額 */
        .total {
            width: 800px;
            margin: 20px auto 0;
            text-align: right;
            font-size: 20px;
            font-weight: bold;
        }

        /* 商品一覧へ */
        .back {
            width: 800px;
            margin: 20px auto 0;
            text-align: left;
        }
    </style>
</head>

<body>
    <?php	
        $show_header_menu = true;	
        require_once __DIR__ . '/../../htdocs/ec_site/header.php';	
    ?>

    <h1>購入ありがとうございました</h1>

    <div class="message">
        ご購入いただきありがとうございました。
    </div>

    <?php if (!empty($purchase_items)): ?>

        <?php
        $total = 0;
        ?>

        <table>

            <thead>
                <tr>
                    <th>商品</th>
                    <th>価格</th>
                    <th>個数</th>
                    <th>小計</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($purchase_items as $item): ?>

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

                                    <span>
                                        画像なし
                                    </span>

                                <?php endif; ?>

                                <span class="product-name">
                                    <?= htmlspecialchars(
                                        $item['product_name'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </span>

                            </div>

                        </td>

                        <!-- 価格 -->
                        <td>
                            ¥ <?= number_format(
                                (int)$item['price']
                            ) ?>
                        </td>

                        <!-- 個数 -->
                        <td>
                            <?= htmlspecialchars(
                                $item['product_qty'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                            個
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

    <?php endif; ?>

    <div class="back">
        <a href="products.php">
            商品一覧へ戻る
        </a>
    </div>

</body>

</html>
