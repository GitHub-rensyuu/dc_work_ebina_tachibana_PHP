<?php
//Model (model.php)を読み込む
require_once '../../include/model/work39_model.php';

// 投稿タイトル、エラーメッセージ、成功メッセージ、投稿一覧の変数を設定
$title = '';
$error = validate_image_post(
    $_POST['title'] ?? '',
    $_FILES['upload_image'] ?? null
);
$message = '';
$error = '';
$posts = [];

$db = connect_database();

// 画像投稿
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['upload'])
) {
    $title = $_POST['title'] ?? '';

    $error = validate_image_post(
        $title,
        $_FILES['upload_image'] ?? null
    );

    if (empty($error)) {
        [$message, $upload_error] = handle_image_upload(
            $db,
            $title,
            $_FILES['upload_image']
        );

        if (!empty($upload_error)) {
            $error = $upload_error;
        }
    }
}

// 全削除ボタンを押したときに、imageテーブルの全データ削除
if (isset($_POST['deleteAll'])) {
  handle_delete_all($db);
}

// 削除ボタンを押したときに、imageテーブルのデータ削除
// 個別削除
if (isset($_POST['delete'])) {

  $image_id = (int)$_POST['image_id'];

  // 削除する画像名を取得
  $image = get_image($db, $image_id);

  if ($image) {
      if (delete_image($db, $image_id)) {
          // imgフォルダから削除
          $path = "img/" . $image['file_name'];

          if (file_exists($path)) {
              unlink($path);
          }
          $message = "画像を削除しました。";
      } else {
          $error = "削除に失敗しました。";
      }

  } else {
      $error = "画像が見つかりません。";
  }
}

// 公開・非公開切り替え
if (isset($_POST['change_public'])) {
    if (
        isset($_POST['image_id']) &&
        isset($_POST['public_flg'])
    ) {
        $message = update_public(
            $db,
            (int)$_POST['image_id'],
            (int)$_POST['public_flg']
        );
    } else {
        $error = 'データが不足しています。';
    }
}

$posts = show_images($db);

//View(view.php)読み込み
include_once '../../include/view/work39_view.php';