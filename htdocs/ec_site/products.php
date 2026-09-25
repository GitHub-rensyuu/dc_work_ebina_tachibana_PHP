<?php

require_once __DIR__ . '/../../include/common/session.php';
require_once __DIR__ . '/../../include/common/auth.php';

require_user();

require_once __DIR__ . '/../../include/model/product_model.php';
require_once __DIR__ . '/../../include/common/database.php';

$db = connect_database();

$user_id = (int)$_SESSION['user_id'];

// POST処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    if (
        isset($_SESSION['admin_flg']) &&
        (int)$_SESSION['admin_flg'] === 1
    ) {
        header('Location: admin_products.php');
        exit;
    }

    if (isset($_POST['add_cart'])) {
        $product_id = filter_input(
            INPUT_POST,
            'product_id',
            FILTER_VALIDATE_INT
        );

        if ($product_id === false || $product_id === null) {
            $_SESSION['cart_message'] =
                '商品情報が正しくありません。';
        } else {
            $_SESSION['cart_message'] = add_cart(
                $db,
                $user_id,
                $product_id
            );
        }
    }

    header('Location: products.php');
    exit;
}

// メッセージ取得
$cart_message = $_SESSION['cart_message'] ?? '';
unset($_SESSION['cart_message']);

// 商品検索・並び替え条件取得
$keyword = $_GET['keyword'] ?? '';
$sort = $_GET['sort'] ?? '';

// 商品一覧取得
$products = show_products(
    $db,
    $keyword,
    $sort
);

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>商品一覧ページ</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/products.css">
</head>

<body>

<?php
$show_header_menu = true;
require_once __DIR__ . '/header.php';
?>

<h1>商品一覧</h1>

<form action="products.php" method="get" class="search-form">

    <input
        type="search"
        name="keyword"
        value="<?= htmlspecialchars(
            $keyword,
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
        placeholder="商品名を入力"
    >

    <button type="submit">
        検索
    </button>

</form>

<form action="products.php" method="get" class="sort-form">

    <input
        type="hidden"
        name="keyword"
        value="<?= htmlspecialchars(
            $keyword,
            ENT_QUOTES,
            'UTF-8'
        ) ?>"
    >

    <label for="sort">並び順</label>

    <select name="sort">
        <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>
            古い順
        </option>

        <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>
            新しい順
        </option>

        <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>
            価格が安い順
        </option>

        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>
            価格が高い順
        </option>
    </select>


    <button type="submit">
        並び替え
    </button>

</form>

<?php if ($cart_message !== ''): ?>
    <p class="cart-message">
        <?= htmlspecialchars(
            $cart_message,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </p>
<?php endif; ?>

<hr class="section-divider">

<ul class="product-list">

<?php foreach ($products as $product): ?>

    <?php
    if ((int)$product['public_flg'] !== 1) {
        continue;
    }

    $is_sold_out =
        (int)$product['stock_qty'] === 0;
    ?>

    <li class="product-card <?= $is_sold_out ? 'sold-out' : '' ?>">

        <div class="image-wrapper">

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

            <?php if ($is_sold_out): ?>
                <div class="sold-out-label">
                    売り切れ
                </div>
            <?php endif; ?>

        </div>

        <div class="product-info">

            <div class="product-name-price">

                <span class="product-name">
                    <?= htmlspecialchars(
                        $product['product_name'],
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </span>

                <span class="price">
                    <?= number_format(
                        (int)$product['price']
                    ) ?>円
                </span>

            </div>

            <?php if (!$is_sold_out): ?>

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

                    <button
                        type="submit"
                        name="add_cart"
                        class="cart-button"
                    >
                        カートに入れる
                    </button>

                </form>

            <?php endif; ?>

        </div>

    </li>

<?php endforeach; ?>

</ul>

</body>
</html>