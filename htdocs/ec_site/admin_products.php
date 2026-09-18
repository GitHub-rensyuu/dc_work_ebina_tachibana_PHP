<?php

// ==============================
// セッション開始
// ==============================
require_once __DIR__ . '/../../include/common/session.php';

// ==============================
// 共通認証処理を読み込む
// ==============================
require_once __DIR__ . '/../../include/common/auth.php';
require_admin();

// ==============================
// Modelを読み込む
// ==============================
require_once __DIR__ . '/../../include/model/admin_products_model.php';
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
    // ログアウト
    // ==============================
    if (isset($_POST['logout'])) {

        logout();

    // ==============================
    // 商品登録
    // ==============================
    } elseif (isset($_POST['register'])) {

        $product_name = $_POST['product_name'] ?? '';
        $price = $_POST['price'] ?? '';
        $stock_qty = $_POST['stock_qty'] ?? '';
        $public_flg = $_POST['public_flg'] ?? '';

        $error = validate_product_post(
            $product_name,
            $price,
            $stock_qty,
            $public_flg,
            $_FILES['product_image'] ?? null
        );

        if (!empty($error)) {
            // エラーメッセージを保存
            $_SESSION['admin_product_error'] = $error;
            $_SESSION['admin_product_message_type'] = 'register';
        } else {
            [$message, $register_error] =
                register_product(
                    $db,
                    $product_name,
                    $price,
                    $stock_qty,
                    $public_flg,
                    $_FILES['product_image']
                );

            if (!empty($register_error)) {
                // 登録失敗
                $_SESSION['admin_product_error'] = $register_error;
                $_SESSION['admin_product_message_type'] = 'register';
            } else {
                // 登録成功
                $_SESSION['admin_product_message'] = $message;
                $_SESSION['admin_product_message_type'] = 'register';
            }
        }
            
    // ==============================
    // 在庫変更
    // ==============================
    } elseif (isset($_POST['change_stock'])) {

        if (
            isset($_POST['product_id']) &&
            isset($_POST['stock_qty'])
        ) {

            $product_id = (int)$_POST['product_id'];
            $stock_qty = $_POST['stock_qty'];

            if (
                $stock_qty === '' ||
                filter_var(
                    $stock_qty,
                    FILTER_VALIDATE_INT
                ) === false ||
                $stock_qty < 0
            ) {

                $_SESSION['admin_product_error'] =
                    '正しい在庫数を入力してください。';
                $_SESSION['admin_product_message_type'] = 'product';

            } else {
                $message = update_stock($db,$product_id,(int)$stock_qty);
                $_SESSION['admin_product_message'] = $message;
                $_SESSION['admin_product_message_type'] = 'product';
            }

        } else {
            $_SESSION['admin_product_error'] = 'データが不足しています。';
            $_SESSION['admin_product_message_type'] = 'product';
        }

    // ==============================
    // 価格変更
    // ==============================
    } elseif (isset($_POST['change_price'])) {

        if (
            isset($_POST['product_id']) &&
            isset($_POST['price'])
        ) {

            $product_id = (int)$_POST['product_id'];
            $price = $_POST['price'];

            if (
                $price === '' ||
                filter_var(
                    $price,
                    FILTER_VALIDATE_INT
                ) === false ||
                $price < 0
            ) {

                $_SESSION['admin_product_error'] =
                    '正しい価格を入力してください。';
                $_SESSION['admin_product_message_type'] = 'product';

            } else {

                $message = update_price($db,$product_id,(int)$price);

                $_SESSION['admin_product_message'] = $message;
                $_SESSION['admin_product_message_type'] = 'product';
            }

        } else {

            $_SESSION['admin_product_error'] = 'データが不足しています。';
            $_SESSION['admin_product_message_type'] = 'product';
        }

    // ==============================
    // 公開・非公開
    // ==============================
    } elseif (isset($_POST['change_public'])) {
        if (isset($_POST['product_id'])) {

            $_SESSION['admin_product_message'] =
                update_public($db,(int)$_POST['product_id']);
            $_SESSION['admin_product_message_type'] = 'product';

        } else {

            $_SESSION['admin_product_error'] = 'データが不足しています。';
            $_SESSION['admin_product_message_type'] = 'product';
        }

    // ==============================
    // 商品削除
    // ==============================
    } elseif (isset($_POST['delete_product'])) {
        if (isset($_POST['product_id'])) {
            $_SESSION['admin_product_message'] = delete_product($db,(int)$_POST['product_id']
            );
            $_SESSION['admin_product_message_type'] = 'product';
        } else {
            $_SESSION['admin_product_error'] = 'データが不足しています。';
            $_SESSION['admin_product_message_type'] = 'product';
        }
    }

    // ==============================	
    // PRG	
    // POST → Redirect → GET	
    // ==============================	
    header('Location: admin_products.php');	
    exit();

}

// ==============================	
// GET時にメッセージを取得	
// ==============================	
$message = $_SESSION['admin_product_message'] ?? '';
$error = $_SESSION['admin_product_error'] ?? '';
$message_type = $_SESSION['admin_product_message_type'] ?? '';

// 一度表示したら削除
unset($_SESSION['admin_product_message']);
unset($_SESSION['admin_product_error']);
unset($_SESSION['admin_product_message_type']);

// ==============================
// 商品一覧を取得
// ==============================
$products = show_products($db);

// ==============================
// View読み込み
// ==============================
include_once __DIR__ . '/../../include/view/admin_products_view.php';