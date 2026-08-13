<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>TRY50</title>
</head>
<body>
  <?php
    $global_variable = "グローバル変数";

    function set_local_variable(){
      $local_variable = "ローカル変数";

      echo "<p>関数内のローカル変数:" . $local_variable . "</p>";
      echo "<p>関数内のグローバル変数:" . $global_variable . "</p>";
    }

    set_local_variable();

    echo "<p>関数外のグローバル変数:" . $global_variable . "</p>";
    echo "<p>関数外のローカル変数:" . $local_variable . "</p>";
  ?>
</body>
</html>