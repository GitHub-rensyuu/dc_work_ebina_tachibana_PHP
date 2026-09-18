<?php

require_once __DIR__ . '/../../include/common/session.php';
require_once __DIR__ . '/../../include/common/auth.php';

require_login();

require_once __DIR__ . '/../../include/model/cart_model.php';
require_once __DIR__ . '/../../include/common/database.php';

$db = connect_database();

$user_id = (int)$_SESSION['user_id'];

// POST処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();

    // 購入処理
    if (isset($_POST['purchase'])) {
        $purchase_items = show_cart(
            $db,
            $user_id
        );

        if (empty($purchase_items)) {
            header('Location: cart.php');
            exit;
        }

        try {
            $db->beginTransaction();

            foreach ($purchase_items as $item) {
                $result = reduce_stock(
                    $db,
                    (int)$item['product_id'],
                    (int)$item['product_qty']
                );

                if (!$result) {
                    throw new RuntimeException(
                        $item['product_name'] .
                        'の在庫が不足しています。'
                    );
                }
            }

            foreach ($purchase_items as $item) {
                if (
                    !delete_cart(
                        $db,
                        $user_id,
                        (int)$item['cart_id']
                    )
                ) {
                    throw new RuntimeException(
                        'カート商品の削除に失敗しました。'
                    );
                }
            }

            $db->commit();

            $_SESSION['purchase_items'] =
                $purchase_items;

            header('Location: complete.php');
            exit;
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            error_log($e->getMessage());

            $_SESSION['cart_error'] =
                $e instanceof RuntimeException
                    ? $e->getMessage()
                    : '購入処理に失敗しました。';

            header('Location: cart.php');
            exit;
        }
    }

    // 個数変更
    if (isset($_POST['change_qty'])) {
        $cart_id = filter_input(
            INPUT_POST,
            'cart_id',
            FILTER_VALIDATE_INT
        );

        $product_qty = filter_input(
            INPUT_POST,
            'product_qty',
            FILTER_VALIDATE_INT
        );

        if (
            $cart_id !== false &&
            $cart_id !== null &&
            $product_qty !== false &&
            $product_qty !== null &&
            $product_qty >= 1
        ) {
            change_cart_qty(
                $db,
                $user_id,
                $cart_id,
                $product_qty
            );
        }

        header('Location: cart.php');
        exit;
    }

    // 商品削除
    if (isset($_POST['delete_cart'])) {
        $cart_id = filter_input(
            INPUT_POST,
            'cart_id',
            FILTER_VALIDATE_INT
        );

        if ($cart_id !== false && $cart_id !== null) {
            delete_cart(
                $db,
                $user_id,
                $cart_id
            );
        }

        header('Location: cart.php');
        exit;
    }

    // 不正なPOST
    header('Location: cart.php');
    exit;
}

// カート情報取得
$cart_items = show_cart(
    $db,
    $user_id
);

// エラーメッセージ取得
$cart_error = $_SESSION['cart_error'] ?? '';
unset($_SESSION['cart_error']);

// View読み込み
include_once __DIR__ . '/../../include/view/cart_view.php';