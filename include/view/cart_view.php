<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>カート</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/cart_view.css">
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

                                    <span class="no-image">
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

                        <!-- 削除 -->
                        <td>
                            <form action="cart.php" method="post">

                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= htmlspecialchars(
                                        get_csrf_token(),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                >

                                <input
                                    type="hidden"
                                    name="cart_id"
                                    value="<?= (int)$item['cart_id'] ?>"
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
                            <form
                                action="cart.php"
                                method="post"
                                class="qty-form"
                            >

                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= htmlspecialchars(
                                        get_csrf_token(),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>"
                                >

                                <input
                                    type="hidden"
                                    name="cart_id"
                                    value="<?= (int)$item['cart_id'] ?>"
                                >

                                <input
                                    type="number"
                                    name="product_qty"
                                    value="<?= (int)$item['product_qty'] ?>"
                                    min="1"
                                    max="<?= (int)$item['stock_qty'] ?>"
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

        <form
            action="cart.php"
            method="post"
            class="purchase-form"
        >

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(
                    get_csrf_token(),
                    ENT_QUOTES,
                    'UTF-8'
                ) ?>"
            >

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
