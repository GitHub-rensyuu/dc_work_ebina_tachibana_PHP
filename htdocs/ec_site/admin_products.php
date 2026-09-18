<?php

require_once __DIR__ . '/../../include/common/session.php';
require_once __DIR__ . '/../../include/common/auth.php';
require_admin();

require_once __DIR__ . '/../../include/model/admin_products_model.php';
require_once __DIR__ . '/../../include/model/product_model.php';

require_once __DIR__ . '/../../include/common/database.php';
$db = connect_database();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();

    if (isset($_POST['logout'])) {
        logout();
    } elseif (isset($_POST['register'])) {
        $product_name = $_POST['product_name'] ?? '';
        $price = $_POST['price'] ?? '';
        $stock_qty = $_POST['stock_qty'] ?? '';
        $public_flg = $_POST['public_flg'] ?? '';
        $product_image = $_FILES['product_image'] ?? null;

        $error = validate_product_post(
            $product_name,
            $price,
            $stock_qty,
            $public_flg,
            $product_image
        );

        if ($error !== '') {
            $_SESSION['admin_product_error'] = $error;
            $_SESSION['admin_product_message_type'] = 'register';
        } else {
            [$message, $register_error] = register_product(
                $db,
                $product_name,
                (int)$price,
                (int)$stock_qty,
                (int)$public_flg,
                $product_image
            );

            if ($register_error !== '') {
                $_SESSION['admin_product_error'] = $register_error;
                $_SESSION['admin_product_message_type'] = 'register';
            } else {
                $_SESSION['admin_product_message'] = $message;
                $_SESSION['admin_product_message_type'] = 'register';
            }
        }
    } elseif (isset($_POST['change_stock'])) {
        if (
            isset($_POST['product_id']) &&
            isset($_POST['stock_qty'])
        ) {
            $product_id = (int)$_POST['product_id'];
            $stock_qty = $_POST['stock_qty'];

            if (
                $stock_qty === '' ||
                filter_var($stock_qty, FILTER_VALIDATE_INT) === false ||
                (int)$stock_qty < 0
            ) {
                $_SESSION['admin_product_error'] =
                    '正しい在庫数を入力してください。';
                $_SESSION['admin_product_message_type'] = 'product';
            } else {
                $message = update_stock(
                    $db,
                    $product_id,
                    (int)$stock_qty
                );

                $_SESSION['admin_product_message'] = $message;
                $_SESSION['admin_product_message_type'] = 'product';
            }
        } else {
            $_SESSION['admin_product_error'] = 'データが不足しています。';
            $_SESSION['admin_product_message_type'] = 'product';
        }
    } elseif (isset($_POST['change_price'])) {
        if (
            isset($_POST['product_id']) &&
            isset($_POST['price'])
        ) {
            $product_id = (int)$_POST['product_id'];
            $price = $_POST['price'];

            if (
                $price === '' ||
                filter_var($price, FILTER_VALIDATE_INT) === false ||
                (int)$price < 0
            ) {
                $_SESSION['admin_product_error'] =
                    '正しい価格を入力してください。';
                $_SESSION['admin_product_message_type'] = 'product';
            } else {
                $message = update_price(
                    $db,
                    $product_id,
                    (int)$price
                );

                $_SESSION['admin_product_message'] = $message;
                $_SESSION['admin_product_message_type'] = 'product';
            }
        } else {
            $_SESSION['admin_product_error'] = 'データが不足しています。';
            $_SESSION['admin_product_message_type'] = 'product';
        }
    } elseif (isset($_POST['change_public'])) {
        if (isset($_POST['product_id'])) {
            $product_id = (int)$_POST['product_id'];

            $_SESSION['admin_product_message'] =
                update_public($db, $product_id);
            $_SESSION['admin_product_message_type'] = 'product';
        } else {
            $_SESSION['admin_product_error'] = 'データが不足しています。';
            $_SESSION['admin_product_message_type'] = 'product';
        }
    } elseif (isset($_POST['delete_product'])) {
        if (isset($_POST['product_id'])) {
            $product_id = (int)$_POST['product_id'];

            $_SESSION['admin_product_message'] =
                delete_product($db, $product_id);
            $_SESSION['admin_product_message_type'] = 'product';
        } else {
            $_SESSION['admin_product_error'] = 'データが不足しています。';
            $_SESSION['admin_product_message_type'] = 'product';
        }
    }

    header('Location: admin_products.php');
    exit();
}

$message = $_SESSION['admin_product_message'] ?? '';
$error = $_SESSION['admin_product_error'] ?? '';
$message_type = $_SESSION['admin_product_message_type'] ?? '';

unset($_SESSION['admin_product_message']);
unset($_SESSION['admin_product_error']);
unset($_SESSION['admin_product_message_type']);

$products = show_products($db);

include_once __DIR__ . '/../../include/view/admin_products_view.php';