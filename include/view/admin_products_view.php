<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ECサイト</title>
  <style>
    table {
      border: solid black 1px;
      width: 800px;
      border-collapse: separate;
    }

    th,td {
      border: 1px solid #bbb;
      padding: 10px;
      white-space: nowrap;
    }

    .product-image {
      padding: 2px;
    }

    .product-image img {
      display: block;
    }

    .public {
      background-color: #fff;
    }

    .private {
      background-color: #ddd;
    }

    caption {
      text-align: left;
    }

  </style>
</head>

<body>
  <h1>商品登録</h1>

  <form method="post" enctype="multipart/form-data">
    商品名:<input type="text" name="product_name"><br>
    価格:<input type="number" name="price"><br>
    個数:<input type="number" name="stock_qty"><br>
    商品画像:<input type="file" name="product_image" accept="image/jpeg,image/png"><br>

    ステータス:
    <select name="public_flg">
      <option value="1">公開</option>
      <option value="0">非公開</option>
    </select>
    <br>

    <input type="submit" name="register" value="商品を登録">
  </form>

  <!-- 商品登録時のメッセージ -->
  <div style="height:24px;">
    <?php if (!empty($error) && isset($_POST['register'])): ?>
      <p style="color:red; margin:0;">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
      </p>
    <?php elseif (!empty($message) && isset($_POST['register'])): ?>
      <p style="color:blue; margin:0;">
        <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
      </p>
    <?php endif; ?>
  </div>  

  <a href="admin_products_gallery.php">商品一覧ページへ</a>
  <a href="index.php">ログアウト</a>
  <br>

  <!-- 商品一覧操作時のメッセージ -->
  <div style="height:24px;">
    <?php if (
      !empty($error)
      && (
        isset($_POST['change_public'])
        || isset($_POST['change_stock'])
        || isset($_POST['delete_product'])
      )
    ): ?>
      <p style="color:red; margin:0;">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
      </p>
    <?php elseif (
      !empty($message)
      && (
        isset($_POST['change_public'])
        || isset($_POST['change_stock'])
        || isset($_POST['delete_product'])
      )
    ): ?>
      <p style="color:blue; margin:0;">
        <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
      </p>
    <?php endif; ?>
  </div>

  <hr style="border:0; border-top:1px solid #bbb; width:100%; margin:20px 0;">
  <br>

  <table>
    <thead>
      <tr>
        <th>商品画像</th>
        <th>商品名</th>
        <th>価格</th>
        <th>在庫数</th>
        <th>公開フラグ</th>
        <th>削除</th>
      </tr>
    </thead>

    <tbody>
      <?php foreach ($products as $product): ?>

        <tr class="<?= $product['public_flg'] == 1 ? 'public' : 'private' ?>">

          <td class="product-image">
            <img
              src="img/<?= htmlspecialchars($product['image_name'], ENT_QUOTES, 'UTF-8') ?>"
              width="200" height="200" alt="商品画像">
          </td>

          <td>
            <?= htmlspecialchars($product['product_name'], ENT_QUOTES, 'UTF-8') ?>
          </td>

          <td>
            ¥ <?= htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8') ?>
          </td>

          <td>
            <form method="post" style="display:flex; align-items:center; gap:5px;">
              <input type="hidden" name="product_id"
                value="<?= htmlspecialchars($product['product_id'], ENT_QUOTES, 'UTF-8') ?>">
              <input type="number" name="stock_qty" value="<?= htmlspecialchars($product['stock_qty'], ENT_QUOTES, 'UTF-8') ?>"
                min="0">
              <input type="submit" name="change_stock" value="変更する">
            </form>
          </td>

          <td>
            <form method="post">
              <input type="hidden" name="product_id"
                value="<?= htmlspecialchars($product['product_id'], ENT_QUOTES, 'UTF-8') ?>"
              >

              <input type="hidden" name="public_flg"
                value="<?= htmlspecialchars($product['public_flg'], ENT_QUOTES, 'UTF-8') ?>"
              >

              <?php if ($product['public_flg'] == 1): ?>
                <input type="submit" name="change_public" value="非表示にする">
              <?php else: ?>
                <input type="submit" name="change_public" value="表示する">
              <?php endif; ?>
            </form>
          </td>

          <td>
            <form method="post">
              <input type="hidden" name="product_id"
              value="<?= htmlspecialchars($product['product_id'], ENT_QUOTES, 'UTF-8') ?>">
              <input type="submit" name="delete_product" value="削除する">
            </form>
          </td>
        </tr>

      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>