<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>商品管理ページ</title>
    <link rel="stylesheet" href="css/admin_products_view.css">
    <link rel="stylesheet" href="css/message.css">
</head>

<body>
    <h1>商品登録</h1>

    <form method="post" enctype="multipart/form-data" class="register-form" novalidate>
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
        <input type="text" name="product_name" required>
        <br>

        価格:
        <input type="number" name="price" min="0" step="1" required>
        <br>

        在庫数:
        <input type="number" name="stock_qty" min="0" step="1" required>
        <br>

        商品画像:
        <input
            type="file"
            id="product_image"
            name="product_image"
            accept="image/jpeg,image/png"
            required
        >
        <br>

        ステータス:
        <select name="public_flg">
            <option value="1">公開</option>
            <option value="0">非公開</option>
        </select>
        <br>

        <!-- フロントエンドのエラー表示 -->
        <p id="product-validation-error" class="message-error" role="alert" hidden>
        </p>

        <input type="submit" name="register" value="商品を登録">
    </form>

    <!-- 商品登録時のメッセージ -->
    <?php if ($message_type === 'register'): ?>

        <div class="message-area">
            <?php if ($error !== ''): ?>

                <p class="message-error">
                    <?= htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

            <?php elseif ($message !== ''): ?>

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

    <!-- ログアウト -->
    <form action="admin_products.php" method="post" class="logout-form">
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
            <?php if ($error !== ''): ?>

                <p class="message-error" role="alert">
                    <?= htmlspecialchars(
                        $error,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </p>

            <?php elseif ($message !== ''): ?>

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
                                value="<?= (int)$product['product_id'] ?>"
                            >

                            <span>¥</span>

                            <input
                                type="number"
                                name="price"
                                value="<?= (int)$product['price'] ?>"
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
                                value="<?= (int)$product['product_id'] ?>"
                            >

                            <input
                                type="number"
                                name="stock_qty"
                                value="<?= (int)$product['stock_qty'] ?>"
                                min="0"
                                step="1"
                                formnovalidate
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
                                value="<?= (int)$product['product_id'] ?>"
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
                                value="<?= (int)$product['product_id'] ?>"
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('.register-form');
            const imageInput = document.getElementById('product_image');
            const errorArea = document.getElementById('product-validation-error');

            form.addEventListener('submit', function (event) {
                errorArea.textContent = '';
                errorArea.hidden = true;

                // HTML標準の入力チェック
                // 必須項目の入力チェック
                if (!form.checkValidity()) {
                    event.preventDefault();

                    if (!form.product_name.value.trim()) {
                        showError('商品名を入力してください。');
                    } else if (form.price.value === '') {
                        showError('価格を入力してください。');
                    } else if (
                        form.price.validity.badInput ||
                        form.price.validity.rangeUnderflow ||
                        !form.price.validity.valid
                    ) {
                        showError('正しい価格を入力してください。');
                    } else if (form.stock_qty.value === '') {
                        showError('在庫数を入力してください。');
                    } else if (
                        form.stock_qty.validity.badInput ||
                        form.stock_qty.validity.rangeUnderflow ||
                        !form.stock_qty.validity.valid
                    ) {
                        showError('正しい在庫数を入力してください。');
                    } else if (imageInput.files.length === 0) {
                        showError('商品画像を選択してください。');
                    }

                    return;
                }

                // 商品画像の確認
                if (imageInput.files.length === 0) {
                    event.preventDefault();
                    showError('商品画像を選択してください。');
                    return;
                }

                const file = imageInput.files[0];
                const allowedTypes = ['image/jpeg', 'image/png'];

                if (!allowedTypes.includes(file.type)) {
                    event.preventDefault();
                    showError(
                        '商品画像はJPGまたはPNG形式を選択してください。'
                    );
                }
            });

            function showError(message) {
                errorArea.textContent = message;
                errorArea.hidden = false;
            }
        });
    </script>

</body>
</html>
