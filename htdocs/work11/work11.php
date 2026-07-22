<?php
  echo '①<br>';
  for ($i = 1;$i <= 100; $i++):
    if ($i % 3 === 0 && $i % 4 === 0){
      echo 'Fizz Buzz';
    } elseif ($i % 3 === 0){
      echo 'Fizz';
    } elseif ($i % 4 === 0){
      echo 'Buzz';
    } else {
      echo $i;
    }
  endfor;
    
  echo '②<br>';
  for ($i = 1;$i <= 9; $i++):
    for ($j = 1;$j <= 9; $j++){
      echo $i . '*' . $j . ' = '. $i * $j . '<br>';
    }
  endfor;

  echo '③<br>';
  for ($i = 1;$i <= 20; $i++):
    if ($i % 2 == 0){
      echo '!<br>';
    } else {
      echo str_repeat('*',($i - 1) / 2 + 1) . '<br>';
    }
  endfor;
?>