<?php

  $db = connect_database();
  $posts = show_images($db);

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

  function show_images($db){
    // 公開中の画像のみ取得
    $sql = "SELECT title, file_name FROM image WHERE public_flg = 1 ORDER BY image_id ASC";
    $stmt = $db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>work39</title>
</head>
<body>
  <h1>画像一覧</h1>
  <a href="admin_products.php">画像投稿ページへ</a>
  <hr style="border:0; border-top:1px solid #bbb; width:100%; margin:20px 0;">

  <ul style="display:flex; flex-wrap:wrap; gap:30px; padding:0;">
    <?php foreach ($posts as $post): ?>
      <li style="padding:30px 10px 10px;
        display:flex;
        flex-direction:column;
        align-items:center;
        list-style:none;
        border:1px solid #bbb;
        background-color: #fff;
      ">
          <span>
              <?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>
          </span>

          <img
              src="img/<?php echo htmlspecialchars($post['file_name'], ENT_QUOTES, 'UTF-8'); ?>"
              width="200"
              height="200"
              style="margin-top:10px;"
              alt="投稿画像"
          >
      </li>
    <?php endforeach; ?>
  </ul>
</body>
</html>

