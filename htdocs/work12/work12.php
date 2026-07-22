<?php
  echo '①<br>';
  $i = 1;
  while ($i <= 100){
    if ($i % 3 === 0 && $i % 4 === 0){
      echo 'Fizz Buzz';
    } elseif ($i % 3 === 0){
      echo 'Fizz';
    } elseif ($i % 4 === 0){
      echo 'Buzz';
    } else {
      echo $i;
    }
    $i++;
  }
    
  echo '②<br>';
  $i = 1;
  while ($i <= 9){
    for ($j = 1;$j <= 9; $j++){
      echo $i . '*' . $j . ' = '. $i * $j . '<br>';
    }
    $i++;
  }

  echo '③<br>';
  $i = 1;
  while ($i <= 20){
    if ($i % 2 == 0){
      echo '!<br>';
    } else {
      echo str_repeat('*',($i - 1) / 2 + 1) . '<br>';
    }
    $i++;
  }
?>