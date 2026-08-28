<?php
// Model (model.php)を読み込む
require_once '../../include/model/admin_products_model.php';

// 投稿タイトル、エラーメッセージ、成功メッセージ、投稿一覧の変数を設定
$product_name = '';
$error = '';
$message = '';
$products = [];

$db = connect_database();

// 商品登録
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['register'])
) {
    $product_name = $_POST['product_name'] ?? '';
    $price = $_POST['price'] ?? '';
    $stock_qty = $_POST['stock_qty'] ?? '';
    $public_flg = $_POST['public_flg'] ?? '';

    // 入力チェック
    $error = validate_product_post(
        $product_name,
        $price,
        $stock_qty,
        $public_flg,
        $_FILES['product_image'] ?? null
    );

    // エラーがなければ画像をアップロード
    if (empty($error)) {
        [$message, $register_error] = register_product(
            $db,
            $product_name,
            $price,
            $stock_qty,
            $public_flg,
            $_FILES['product_image']
        );

        if (!empty($register_error)) {
            $error = $register_error;
        }
    }
}

// 在庫数変更
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['change_stock'])) {
    if (
        isset($_POST['product_id'])
        && isset($_POST['stock_qty'])
    ) {
        $product_id = (int)$_POST['product_id'];
        $stock_qty = $_POST['stock_qty'];

        if (
            $stock_qty === ''
            || filter_var($stock_qty, FILTER_VALIDATE_INT) === false
            || $stock_qty < 0
        ) {
            $error = '正しい在庫数を入力してください。';
        } else {
            $message = update_stock($db,$product_id,(int)$stock_qty);
        }
    } else {
        $error = 'データが不足しています。';
    }
}

// 公開・非公開切り替え
if (isset($_POST['change_public'])) {

    if (
        isset($_POST['product_id']) &&
        isset($_POST['public_flg'])
    ) {
        $message = update_public(
            $db,
            (int)$_POST['product_id']
        );
    } else {
        $error = 'データが不足しています。';
    }
}

// 商品削除
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['delete_product'])
) {
    if (isset($_POST['product_id'])) {
        $message = delete_product($db,(int)$_POST['product_id']);
    } else {
        $error = 'データが不足しています。';
    }
}

$products = show_products($db);

// View読み込み
include_once '../../include/view/admin_products_view.php';