<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>商品管理ページ</title>
    <link rel="stylesheet" href="css/admin_products_view.css">
</head>

<body>
    <h1>商品登録</h1>

    <form method="post" enctype="multipart/form-data">
        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars(
                get_csrf_token(),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

        商品名:
        <input type="text" name="product_name">
        <br>

        価格:
        <input type="number" name="price">
        <br>

        在庫数:
        <input type="number" name="stock_qty">
        <br>

        商品画像:
        <input
            type="file"
            name="product_image"
            accept="image/jpeg,image/png"
        >
        <br>

        ステータス:
        <select name="public_flg">
            <option value="1">公開</option>
            <option value="0">非公開</option>
        </select>
        <br>

        <input type="submit" name="register" value="商品を登録">
    </form>

    <!-- 商品登録時のメッセージ -->
    <?php if ($message_type === 'register'): ?>

        <div class="message-area">
            <?php if (!empty($error)): ?>

                <p class="message-error">
                    <?= htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

            <?php elseif (!empty($message)): ?>

                <p class="message-success">
                    <?= htmlspecialchars(
                        $message,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

            <?php endif; ?>
        </div>

    <?php endif; ?>

    <a href="products.php">商品一覧ページへ</a>

    <!-- ログアウト -->
    <form action="admin_products.php" method="post">
        <input
            type="hidden"
            name="csrf_token"
            value="<?= htmlspecialchars(
                get_csrf_token(),
                ENT_QUOTES,
                'UTF-8'
            ) ?>"
        >

        <input type="submit" name="logout" value="ログアウト">
    </form>

    <br>

    <!-- 商品一覧操作時のメッセージ -->
    <?php if ($message_type === 'product'): ?>

        <div class="message-area">
            <?php if (!empty($error)): ?>

                <p class="message-error">
                    <?= htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

            <?php elseif (!empty($message)): ?>

                <p class="message-success">
                    <?= htmlspecialchars(
                        $message,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

            <?php endif; ?>
        </div>

    <?php endif; ?>

    <hr class="section-divider">

    <table>
        <caption>商品一覧</caption>

        <thead>
            <tr>
                <th>商品画像</th>
                <th>商品名</th>
                <th>価格</th>
                <th>在庫数</th>
                <th>公開フラグ</th>
                <th>削除</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($products as $product): ?>

                <tr class="<?= (int)$product['public_flg'] === 1 ? 'public' : 'private' ?>">

                    <td class="product-image">
                        <?php if (!empty($product['image_name'])): ?>

                            <img
                                src="img/<?= htmlspecialchars(
                                    $product['image_name'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                width="200"
                                height="200"
                                alt="商品画像"
                            >

                        <?php else: ?>

                            <span>画像なし</span>

                        <?php endif; ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(
                            $product['product_name'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </td>

                    <td>
                        <form method="post" class="price-form">
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
                                name="product_id"
                                value="<?= htmlspecialchars(
                                    $product['product_id'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            >

                            <span>¥</span>

                            <input
                                type="number"
                                name="price"
                                value="<?= htmlspecialchars(
                                    $product['price'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                min="0"
                            >

                            <input
                                type="submit"
                                name="change_price"
                                value="変更する"
                            >
                        </form>
                    </td>

                    <td>
                        <form method="post" class="stock-form">
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
                                name="product_id"
                                value="<?= htmlspecialchars(
                                    $product['product_id'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            >

                            <input
                                type="number"
                                name="stock_qty"
                                value="<?= htmlspecialchars(
                                    $product['stock_qty'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                min="0"
                            >

                            <input
                                type="submit"
                                name="change_stock"
                                value="変更する"
                            >
                        </form>
                    </td>

                    <td>
                        <form method="post">
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
                                name="product_id"
                                value="<?= htmlspecialchars(
                                    $product['product_id'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            >

                            <?php if ((int)$product['public_flg'] === 1): ?>

                                <input
                                    type="submit"
                                    name="change_public"
                                    value="非表示にする"
                                >

                            <?php else: ?>

                                <input
                                    type="submit"
                                    name="change_public"
                                    value="表示する"
                                >

                            <?php endif; ?>
                        </form>
                    </td>

                    <td>
                        <form method="post">
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
                                name="product_id"
                                value="<?= htmlspecialchars(
                                    $product['product_id'],
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                            >

                            <input
                                type="submit"
                                name="delete_product"
                                value="削除する"
                            >
                        </form>
                    </td>

                </tr>

            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
