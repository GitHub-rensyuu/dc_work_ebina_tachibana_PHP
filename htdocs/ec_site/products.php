<?php

// ==============================
// セッション開始
// ==============================
session_start();

// ==============================
// CSRFトークン作成
// ==============================
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ==============================
// 共通認証処理を読み込む
// ==============================
require_once __DIR__ . '/../../include/common/auth.php';

// ==============================
// ログイン状態を確認
// ==============================
//
// 未ログインでproducts.phpに直接アクセスした場合
// index.phpへ移動
require_login();

// ==============================
// Model
// ==============================
require_once __DIR__ . '/../../include/model/product_model.php';

// ==============================
// データベース接続
// ==============================
require_once __DIR__ . '/../../include/common/database.php';
$db = connect_database();

// ==============================
// POST処理
// ==============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ==============================
    // CSRFトークン確認
    // ==============================
    verify_csrf_token();

    // ==============================
    // カートに追加
    // ==============================
    if (isset($_POST['add_cart'])) {

        if (isset($_POST['product_id'])) {

            $product_id = (int)$_POST['product_id'];

            $message = add_cart(
                $db,
                $_SESSION['user_id'],
                $product_id
            );

            $_SESSION['cart_message'] = $message;

        } else {

            $_SESSION['cart_message'] =
                'データが不足しています。';
        }
    }

    // ==============================
    // PRG
    // POST → Redirect → GET
    // ==============================
    header('Location: products.php');
    exit();
}

// ==============================
// メッセージ取得
// ==============================
$cart_message = $_SESSION['cart_message'] ?? '';

unset($_SESSION['cart_message']);

// ==============================
// 商品一覧を取得
// ==============================
$products = show_products($db);

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <title>商品一覧ページ</title>
  <link rel="stylesheet" href="css/header.css">

  <style>
    .product-list {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      width: 700px;
      padding: 0;
      margin: 0 auto;
    }

    .product-card {
      width: 220px;
      box-sizing: border-box;
      padding: 2px;
      list-style: none;
      border: 1px solid #bbb;
      background-color: #fff;
    }

    .product-card.sold-out {
      background-color: #ddd;
      color: #666;
    }

    .image-wrapper {
      position: relative;
      width: 200px;
      height: 200px;
      margin: 5px auto;
    }

    .image-wrapper img {
      display: block;
      width: 200px;
      height: 200px;
      object-fit: contain;
    }

    .sold-out .image-wrapper img {
      opacity: 0.45;
    }

    .sold-out-label {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);

      width: 180px;
      padding: 10px 0;

      background-color: rgba(0, 0, 0, 0.75);
      color: #fff;

      font-size: 28px;
      font-weight: bold;
      text-align: center;

      border: 2px solid #fff;
      box-sizing: border-box;
    }

    .product-info {
      padding: 5px 3px 8px;
      text-align: center;
    }

    .product-name-price {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 5px;
      margin-bottom: 5px;
    }

    .product-name {
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .price {
      white-space: nowrap;
    }

    .cart-button {
      width: 60%;
      padding: 3px 0px;
      cursor: pointer;
    }
  </style>
</head>

<body>
  <?php
  $show_header_menu = true;
  require_once __DIR__ . '/header.php';
  ?>

  <h1>商品一覧</h1>

  <?php if (!empty($cart_message)): ?>
    <p style="color:blue;">
      <?= htmlspecialchars($cart_message,ENT_QUOTES,'UTF-8') ?>
    </p>
  <?php endif; ?>

  <a href="admin_products.php">商品登録ページへ</a>

  <hr style="border:0; border-top:1px solid #bbb; width:100%; margin:20px 0;">
  <br>

  <ul class="product-list">

    <?php foreach ($products as $product): ?>

      <?php
      // 非公開の商品は表示しない
      if ((int)$product['public_flg'] !== 1) {
          continue;
      }

      // 在庫数が0なら売り切れ
      $is_sold_out = ((int)$product['stock_qty'] === 0);
      ?>

      <li class="product-card <?= $is_sold_out ? 'sold-out' : '' ?>">

        <!-- 商品画像 -->
        <div class="image-wrapper">

          <img
            src="img/<?= htmlspecialchars($product['image_name'],ENT_QUOTES,'UTF-8') ?>"
            width="200"
            height="200"
            alt="商品画像"
          >

          <?php if ($is_sold_out): ?>
            <div class="sold-out-label">
              売り切れ
            </div>
          <?php endif; ?>

        </div>

        <!-- 商品名・価格・カートボタン -->
        <div class="product-info">

          <div class="product-name-price">

            <span class="product-name">
              <?= htmlspecialchars($product['product_name'],ENT_QUOTES,'UTF-8') ?>
            </span>

            <span class="price">
              <?= number_format((int)$product['price']) ?>円
            </span>

          </div>

          <?php if (!$is_sold_out): ?>
            <form method="post">
              <input type="hidden" name="csrf_token"
                value="<?= htmlspecialchars(
                  $_SESSION['csrf_token'],
                  ENT_QUOTES,
                  'UTF-8'
                ) ?>"
              >

              <input type="hidden" name="product_id"
                 value="<?= htmlspecialchars(
                  $product['product_id'],
                  ENT_QUOTES,
                  'UTF-8'
                ) ?>"
              >

              <button type="submit" name="add_cart" class="cart-button">
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