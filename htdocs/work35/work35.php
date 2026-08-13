<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>work35</title>
</head>
<body>
  <?php
      $random_number = rand(1, 10);
      echo output_function($random_number);

      function output_function($random_number){
          if ($random_number % 2 == 0){
              return $random_number * 10;
          } else {
              return $random_number * 100;
          }
      }
  ?>
</body>
</html>