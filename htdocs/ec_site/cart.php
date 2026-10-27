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
        $purchase_items = show_cart($db, $user_id);

        if (empty($purchase_items)) {
            header('Location: cart.php');
            exit;
        }

        try {
            $db->beginTransaction();

            // 購入直前に公開状態を再確認
            foreach ($purchase_items as $item) {
                $stmt = $db->prepare(
                    'SELECT public_flg
                     FROM ec_product
                     WHERE product_id = ?'
                );

                $stmt->execute([
                    (int)$item['product_id']
                ]);

                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if (
                    $product === false ||
                    (int)$product['public_flg'] !== 1
                ) {
                    throw new RuntimeException(
                        $item['product_name'] .
                            'は現在購入できません。'
                    );
                }
            }

            // 在庫を減らす
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

            // カート商品を削除
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

            $_SESSION['purchase_items'] = $purchase_items;

            header('Location: complete.php');
            exit;
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            error_log(
                '購入処理に失敗しました: ' .
                    $e->getMessage()
            );

            if (
                strpos($e->getMessage(), '在庫が不足しています。') !== false ||
                strpos($e->getMessage(), '現在購入できません。') !== false
            ) {
                $_SESSION['cart_error'] = $e->getMessage();
            } else {
                // その他のエラーは詳細を隠す
                $_SESSION['cart_error'] =
                    '購入処理に失敗しました。時間をおいて再度お試しください。';
            }

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
            $cart_id > 0 &&
            $product_qty !== false &&
            $product_qty !== null &&
            $product_qty >= CART_MIN_QTY
        ) {
            $result = change_cart_qty(
                $db,
                $user_id,
                $cart_id,
                $product_qty
            );

            // エラーの場合はメッセージを保存
            if ($result !== true) {
                $_SESSION['cart_error'] = $result;
                $_SESSION['cart_message_type'] = 'error';
            } else {
                $_SESSION['cart_message'] = '個数を変更しました。';
                $_SESSION['cart_message_type'] = 'success';
            }
        } else {
            $_SESSION['cart_error'] =
                '個数には1以上の整数を指定してください。';
            $_SESSION['cart_message_type'] = 'error';
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

        if (
            $cart_id !== false &&
            $cart_id !== null &&
            $cart_id > 0
        ) {
            $result = delete_cart(
                $db,
                $user_id,
                $cart_id
            );

            if (!$result) {
                $_SESSION['cart_error'] =
                    '商品の削除に失敗しました。';
                $_SESSION['cart_message_type'] = 'error';
            } else {
                $_SESSION['cart_message'] =
                    '商品を削除しました。';
                $_SESSION['cart_message_type'] = 'success';
            }
        } else {
            $_SESSION['cart_error'] =
                '商品情報が正しくありません。';
            $_SESSION['cart_message_type'] = 'error';
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
$cart_message = $_SESSION['cart_message'] ?? '';
$cart_message_type = $_SESSION['cart_message_type'] ?? '';
unset($_SESSION['cart_error']);
unset($_SESSION['cart_message']);
unset($_SESSION['cart_message_type']);

// View読み込み
include_once __DIR__ . '/../../include/view/cart_view.php';
