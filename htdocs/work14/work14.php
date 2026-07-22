<?php
  $num = array();
  for ($i= 1; $i <= 5; $i++){
    array_push($num,rand(1,100));
  }

  for ($i= 0; $i <= count($num) -1; $i++){
    if ($num[$i] % 2 === 0){
      print $num[$i] . '(偶数)';
    } else {
      print $num[$i] . '(奇数)';
    }
  }
?>