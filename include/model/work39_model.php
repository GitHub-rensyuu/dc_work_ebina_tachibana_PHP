<?php
  
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

  // 画像投稿処理
  function handle_image_upload($db, $title, $file){
    // ファイル名を取得
    $filename = basename($file['name']);

    // 拡張子を取得
    $extension = pathinfo($filename, PATHINFO_EXTENSION);

    // ユニークなファイル名を作成
    $new_filename = uniqid() . '.' . $extension;

    // 保存先
    $save = 'img/' . $new_filename;


    // imgフォルダが無ければ作成
    if (!is_dir('img')) {
        mkdir('img', 0777, true);
    }


    // 一時ファイルをimgフォルダへ移動
    if (
        !move_uploaded_file(
            $file['tmp_name'],
            $save
        )
    ) {
        return ['', 'アップロード失敗しました。'];
    }


    try {

        // DBに画像情報を登録
        if (insert_image($db, $title, $new_filename)) {

            return ['アップロード成功しました。', ''];

        } else {

            // DB登録に失敗したら保存した画像を削除
            unlink($save);

            return ['', '登録に失敗しました。'];
        }

    } catch (PDOException $e) {

        // DB登録に失敗したら保存した画像を削除
        if (file_exists($save)) {
            unlink($save);
        }

        return ['', $e->getMessage()];
    }
  }

  function delete_all_images($db){
    $db->exec("DELETE FROM image");
  }

  function handle_delete_all($db){
        try {
            delete_all_images($db);

            foreach (glob('img/*') as $image) {
                if (is_file($image)) {
                    unlink($image);
                }
            }

            return '全削除しました。';

        } catch (PDOException $e) {
            return '削除エラー：' . $e->getMessage();
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

function validate_image_post($title, $file){
    if (empty($title)) {
        return '投稿タイトルを入力してください。';
    }
    if (
        !isset($file) ||
        $file['error'] !== UPLOAD_ERR_OK
    ) {
        return '画像ファイルを選択してください。';
    }
    $type = mime_content_type($file['tmp_name']);
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

  function update_public($db, $image_id, $public_flg){
    $new_flg = ($public_flg == 1) ? 0 : 1;
    $stmt = $db->prepare(
        "UPDATE image SET public_flg=? WHERE image_id=?"
    );
    if ($stmt->execute([$new_flg, $image_id])) {
        return ($new_flg == 1)
            ? "公開しました。"
            : "非公開にしました。";
    }
    return "更新に失敗しました。";
  }

  function show_images($db){
    // imageの画像を表示
    $stmt = $db->query("SELECT * FROM image ORDER BY image_id ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  
?>