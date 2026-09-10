<?php

// ==============================
// セッション開始
// ==============================
session_start();

// ==============================
// Cookieの保存期間
// ==============================
require_once __DIR__ . '/../../include/common/cookie.php';

// ==============================
// 共通認証処理を読み込む
// ==============================
require_once __DIR__ . '/../../include/common/auth.php';

// ==============================
// ログイン状態を確認
// ==============================
//
// 未ログインでcart.phpに直接アクセスした場合
// ログインページへ移動
require_login();

// ==============================
// ログインユーザーID取得
// ==============================
$user_id = $_SESSION['user_id'];

// ==============================
// データベース接続
// ==============================
require_once __DIR__ . '/../../include/common/database.php';
$db = connect_database();

// ==============================
// Modelを読み込む
// ==============================
require_once __DIR__ . '/../../include/model/cart_model.php';

// ==============================
// カート操作
// ==============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ==============================
    // 商品購入
    // ==============================
    if (isset($_POST['purchase'])) {

        // カートの商品を取得
        $purchase_items = show_cart($db, $user_id);

        // カートが空の場合
        if (empty($purchase_items)) {
            header('Location: cart.php');
            exit;
        }

        // ==============================
        // 在庫確認・在庫減少
        // ==============================
        foreach ($purchase_items as $item) {

            $result = reduce_stock(
                $db,
                $item['product_id'],
                $item['product_qty']
            );

            // 在庫不足の場合
            if (!$result) {

                $_SESSION['cart_error'] =
                    $item['product_name'] . 'の在庫が不足しています。';

                header('Location: cart.php');
                exit;
            }
        }

        // ==============================
        // 購入商品をセッションに保存
        // ==============================
        $_SESSION['purchase_items'] = $purchase_items;

        // ==============================
        // カートから商品を削除
        // ==============================
        foreach ($purchase_items as $item) {

            delete_cart(
                $db,
                $user_id,
                $item['cart_id']
            );
        }

        // ==============================
        // 購入完了ページへ
        // ==============================
        header('Location: complete.php');
        exit;
    }

    // ==============================
    // 個数変更
    // ==============================
    if (isset($_POST['change_qty'])) {

        $cart_id = (int)$_POST['cart_id'];
        $product_qty = (int)$_POST['product_qty'];

        // 個数は1個以上
        if ($product_qty >= 1) {

            change_cart_qty(
                $db,
                $user_id,
                $cart_id,
                $product_qty
            );
        }

        // 変更後にcart.phpへ戻る
        header('Location: cart.php');
        exit;
    }


    // ==============================
    // 商品削除
    // ==============================
    if (isset($_POST['delete_cart'])) {

        $cart_id = (int)$_POST['cart_id'];

        delete_cart(
            $db,
            $user_id,
            $cart_id
        );

        // 削除後にcart.phpへ戻る
        header('Location: cart.php');
        exit;
    }
}

// ==============================
// カート情報取得
// ==============================
$cart_items = show_cart($db, $user_id);

// ==============================
// エラーメッセージ取得
// ==============================
$cart_error = $_SESSION['cart_error'] ?? '';
unset($_SESSION['cart_error']);

// ==============================
// View読み込み
// ==============================
include_once __DIR__ . '/../../include/view/cart_view.php';