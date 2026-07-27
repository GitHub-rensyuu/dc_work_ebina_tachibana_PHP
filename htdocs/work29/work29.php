<?php
  $host = 'localhost';
  $login_user = 'xb513874_h8646';
  $password = '1r86160zfh';
  $database = 'xb513874_g1gw7';
  $error_msg = [];
  $product = [];
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>work29</title>
</head>
<body>
  <?php
    // データベースへ接続
    $db = new mysqli($host, $login_user, $password, $database);
    if ($db->connect_error){
      echo $db->connect_error;
      exit();
    } else {
      $db->set_charset("utf8"); //文字コードをUTF8に指定
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $db->begin_transaction(); //トラザクション開始

      $sql = "";
      if (isset($_POST["insert"])) {
              $sql = "
              INSERT INTO product (
                  product_id,
                  product_code,
                  product_name,
                  price,
                  category_id
              )
              VALUES (
                  21,
                  1021,
                  'エシャロット',
                  200,
                  1
              )";

          } elseif (isset($_POST["delete"])) {
              $sql = "
              DELETE FROM product
              WHERE product_id = 21";
          }

      if ($db->query($sql)) {
        $row = $db->affected_rows;
      } else {
        $error_msg[] = 'INSERT実行エラー[実行SQL]' . $sql;
      }
      // $error_msg[] = '強制的にエラーメッセージを挿入';

      // エラーメッセージ格納の有無によりトラザクションの成否を判定

      if (empty($error_msg)) {
        echo $row . '件更新しました。';
        $db->commit(); //正常に終了したらコミット
      } else {
        echo '更新が失敗しました。';
        $db->rollback(); //エラーが起きたらロールバック
      }
      // 下記はエラー確認用。エラー確認が必要な際にはコメントを外してください。
      // var_dump($error_msg);

    }

    $select = "SELECT product_id ,product_code,product_name, price,category_id FROM product WHERE product_id = 21;";

    if ($result = $db->query($select)) {
      // 連想配列を取得
      $product = [];
      if ($row = $result->fetch_assoc()) {
        $product = $row;
      }
      // 結果セットを閉じる
      $result->close();
    }

    $db->close(); //接続を閉じる
  ?>

  <form method="post">
    <table border="1">
      <tr>
        <th>項目</th>
        <th>値</th>
      </tr>
      <tr>
        <td>product_id</td>
        <td><?= $product['product_id'] ?? '' ?></td>
      </tr>
      <tr>
        <td>product_code</td>
        <td><?= $product['product_code'] ?? '' ?></td>
      </tr>
      <tr>
        <td>product_name</td>
        <td><?= $product['product_name'] ?? '' ?></td>
      </tr>
      <tr>
        <td>price</td>
        <td><?= $product['price'] ?? '' ?></td>
      </tr>
      <tr>
        <td>category_id</td>
        <td><?= $product['category_id'] ?? '' ?></td>
      </tr>
    </table>
    <input type="submit" name="insert" value="挿入">
    <input type="submit" name="delete" value="削除">
  </form>

</body>
</html>