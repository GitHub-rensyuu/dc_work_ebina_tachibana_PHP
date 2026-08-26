<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>work39</title>
</head>
<body>
  <h1>画像投稿</h1>

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
    画像タイトル:<input type="text" name="title"><br>
    画像:<input type="file" name="upload_image" accept="image/jpeg,image/png"><br>
    <input type="submit" name="upload" value="画像投稿">
  </form>

  <form method="post">
    <input type="submit" name="deleteAll" value="投稿を全削除">
  </form>

  <a href="work39_gallery.php">画像一覧ページへ</a>

  <hr style="border:0; border-top:1px solid #bbb; width:100%; margin:20px 0;">

  <h2>投稿された画像</h2>

  <ul style="display:flex; flex-wrap:wrap; gap:30px; padding:0;">
    <?php foreach ($posts as $post): ?>

    <li style="padding:30px 10px 10px;
      display:flex;
      flex-direction:column;
      align-items:center;
      list-style:none;
      border:1px solid #bbb;
      background-color: <?= $post['public_flg'] == 0 ? '#ddd' : '#fff' ?>;
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

        <form method="post">
            <input type="hidden" name="image_id" value="<?= $post['image_id'] ?>">
            <input type="hidden" name="public_flg" value="<?= $post['public_flg'] ?>">

            <?php if($post['public_flg'] == 1): ?>
                <input type="submit" name="change_public" value="非表示にする">
            <?php else: ?>
                <input type="submit" name="change_public" value="表示する">
            <?php endif; ?>
        </form>

        <form method="post">
          <input type="hidden" name="image_id" value="<?= $post['image_id'] ?>">
          <input type="submit" name="delete" value="削除" onclick="return confirm('この画像を削除しますか？');">
      </form>

    </li>
    <?php endforeach; ?>
  </ul>
</body>
</html>