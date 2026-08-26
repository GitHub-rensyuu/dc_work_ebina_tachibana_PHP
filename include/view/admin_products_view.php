<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>work39</title>
</head>
<body>
  <h1>商品登録</h1>

<!-- 投稿成否判定メッセージ -->
<div style="height:24px;">
  <?php if (!empty($error)): ?>
    <p style="color:red; margin:0;">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </p>
  <?php elseif (!empty($message)): ?>
    <p style="color:blue; margin:0;">
        <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
    </p>
  <?php endif; ?>
</div>

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

  <a href="admin_products_gallery.php">商品一覧ページへ</a>

  <hr style="border:0; border-top:1px solid #bbb; width:100%; margin:20px 0;">
  <br>

  <ul style="display:flex; flex-wrap:wrap; gap:30px; padding:0;">
    <?php foreach ($products as $product): ?>

      <li style="padding:30px 10px 10px;
        display:flex;
        flex-direction:column;
        align-items:center;
        list-style:none;
        border:1px solid #bbb;
        background-color: <?= $product['public_flg'] == 0 ? '#ddd' : '#fff' ?>;
      ">
          <span>
              <?= htmlspecialchars($product['product_name'],ENT_QUOTES,'UTF-8') ?>
          </span>
          <span>
              価格：<?= htmlspecialchars($product['price'],ENT_QUOTES,'UTF-8') ?>円
          </span>
          <span>
              在庫：<?= htmlspecialchars($product['stock_qty'], ENT_QUOTES, 'UTF-8') ?>個
          </span>

          <img
              src="img/<?= htmlspecialchars($product['image_name'],ENT_QUOTES,'UTF-8') ?>"
              width="200"
              height="200"
              style="margin-top:10px;"
              alt="投稿画像"
          >

          <form method="post">
              <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
              <input type="hidden" name="public_flg" value="<?= $product['public_flg'] ?>">

              <?php if($product['public_flg'] == 1): ?>
                  <input type="submit" name="change_public" value="非表示にする">
              <?php else: ?>
                  <input type="submit" name="change_public" value="表示する">
              <?php endif; ?>
          </form>
      </li>
    <?php endforeach; ?>
  </ul>
</body>
</html>