<?php
  $db = connect_database();

  function connect_database(){
    // データベース接続情報
    $host = 'localhost';
    $login_user = 'xb513874_h8646';
    $password = '1r86160zfh';
    $database = 'xb513874_g1gw7';
  
    // データベースへ接続、文字コード設定
    try {
        $db = new PDO(
            "mysql:host=$host;dbname=$database;charset=utf8",
            $login_user,
            $password
        );

        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        die($e->getMessage());
    }
  }
  
  // 投稿タイトル、エラーメッセージ、成功メッセージ、投稿一覧の変数を設定
  $title = '';
  $error = '';
  $message = '';
  $posts = [];

  // 全削除ボタンを押したときに、imageテーブルの全データ削除
  if (isset($_POST['deleteAll'])) {
    try {
        delete_all_images($db);
    } catch (PDOException $e) {
        $error = "削除エラー：" . $e->getMessage();
    }

    // imgフォルダ内の全ファイルを削除
    foreach (glob('img/*') as $image) {
        if (is_file($image)) {
            unlink($image);
        }
    }
    $message = "全削除しました。";
  }

  function delete_all_images($db){
    $db->exec("DELETE FROM image");
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

  function get_image($db, $image_id){
    $stmt = $db->prepare(
        "SELECT file_name FROM image WHERE image_id=?"
    );
    $stmt->execute([$image_id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  function delete_image($db, $image_id){
    $stmt = $db->prepare(
        "DELETE FROM image WHERE image_id=?"
    );
    return $stmt->execute([$image_id]);
  }


  // タイトル・書き込み内容のチェック
  if (!empty($_POST['title'])
    && !isset($_POST['deleteAll'])
    && !isset($_POST['change_public'])
    && !isset($_POST['delete'])) {
    // 画像チェック
    $error = validate_image_post();
    if (empty($error)) {
      $title = $_POST['title'];
      $filename = basename($_FILES['upload_image']['name']);
      $extension = pathinfo($filename, PATHINFO_EXTENSION);
      // 画像ファイルをユニークな名前に変換し、重複しないようにする
      $new_filename = uniqid() . '.' . $extension;
      $save = 'img/' . $new_filename;

      // imgフォルダが無ければ作成する
      if (!is_dir('img')) {
          mkdir('img', 0777, true);
      }

      // 画像ファイルを一時フォルダからimgフォルダに移動する
      if (move_uploaded_file($_FILES['upload_image']['tmp_name'], $save)) {

        try {
            if (insert_image($db, $title, $new_filename)) {
                $message = 'アップロード成功しました.';
            } else {
                unlink($save);
                $error = "登録に失敗しました。";
            }

        } catch (PDOException $e) {
            //DB登録とファイル保存の状態をできるだけ一致させるため、ファイル保存も削除する
            unlink($save);
            $error = $e->getMessage();
        }

      } else {
        $error = 'アップロード失敗しました。';
      }
    }
  } elseif ($_SERVER["REQUEST_METHOD"] == "POST"
      && !isset($_POST['deleteAll'])
      && !isset($_POST['change_public'])
      && !isset($_POST['delete'])) {
      $error = '入力情報が不足しています';
  }


  function validate_image_post() {
    if (empty($_POST['title'])) {
        return '投稿タイトルを入力してください。';
    }
    if (
        !isset($_FILES['upload_image']) ||
        $_FILES['upload_image']['error'] !== UPLOAD_ERR_OK
    ) {
        return '画像ファイルを選択してください。';
    }
    $type = mime_content_type($_FILES['upload_image']['tmp_name']);

    if (!in_array($type, ['image/jpeg', 'image/png'])) {
        return 'ファイルの形式が正しくありません（jpgまたはpng形式の画像のみアップロードできます。）';
    }
    return '';
  }

  // imageテーブルのtiltle,file_nameのデータを保存
  function insert_image($db, $title, $file_name){
    $stmt = $db->prepare(
        "INSERT INTO image(title,file_name) VALUES(?, ?)"
    );

    return $stmt->execute([$title, $file_name]);
  }
  
// 公開・非公開切り替え
  if(isset($_POST['change_public'])){
     $message = update_public($db);
  }

  function update_public($db){
      if (!isset($_POST['image_id']) || !isset($_POST['public_flg'])) {
          return "データが不足しています。";
      }
      $image_id = (int)$_POST['image_id'];
      $public_flg = (int)$_POST['public_flg'];

      // 現在の値を反転
      $new_flg = ($public_flg == 1) ? 0 : 1;

      $stmt = $db->prepare(
          "UPDATE image SET public_flg=? WHERE image_id=?"
      );
      if ($stmt->execute([$new_flg, $image_id])) {
          return ($new_flg == 1) ? "公開しました。" : "非公開にしました。";
      }
      return "更新に失敗しました。";
  }

  $posts = show_images($db);
  function show_images($db){
    // imageの画像を表示
    $stmt = $db->query("SELECT * FROM image ORDER BY image_id ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  function show_message($error, $message){
    if (!empty($error)) {
        echo '<p style="color:red; margin:0;">'
            . htmlspecialchars($error, ENT_QUOTES, 'UTF-8')
            . '</p>';
    } elseif (!empty($message)) {
        echo '<p style="color:blue; margin:0;">'
            . htmlspecialchars($message, ENT_QUOTES, 'UTF-8')
            . '</p>';
    }
  }
  
?>