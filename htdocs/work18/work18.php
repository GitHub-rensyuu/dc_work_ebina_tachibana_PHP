<?php
define('MAX','3'); // 1ページの表示数
 
$customers = array( // 表示データの配列
          array('name' => '佐藤', 'age' => '10'),
          array('name' => '鈴木', 'age' => '15'),
          array('name' => '高橋', 'age' => '20'),
          array('name' => '田中', 'age' => '25'),
          array('name' => '伊藤', 'age' => '30'),
          array('name' => '渡辺', 'age' => '35'),
          array('name' => '山本', 'age' => '40'),
            );

$customers_num = count($customers); // トータルデータ件数
 
$max_page = ceil($customers_num / MAX); // トータルページ数


// データ表示、ページネーションを実装
// 現在のページ
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// 範囲外対策
if ($page < 1) {
    $page = 1;
}
if ($page > $max_page) {
    $page = $max_page;
}

// 取得開始位置
$start = ($page - 1) * MAX;

// 現在のページのデータ
$page_customers = array_slice($customers, $start, MAX);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ページネーション</title>
</head>
<body>

<!-- 表を表示 -->
<h2>名前一覧</h2>

<table border="1">
    <tr>
        <th>名前</th>
        <th>年齢</th>
    </tr>

    <?php foreach ($page_customers as $customer): ?>
    <tr>
        <td><?php echo htmlspecialchars($customer['name'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($customer['age'], ENT_QUOTES, 'UTF-8'); ?></td>
    </tr>
    <?php endforeach; ?>

</table>

<!-- ページ番号表示 -->
<p>
<?php for ($i = 1; $i <= $max_page; $i++): ?>
    <?php if ($i == $page): ?>
        <strong><?php echo $i; ?></strong>
    <?php else: ?>
        <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
    <?php endif; ?>
<?php endfor; ?>
</p>

</body>
</html>