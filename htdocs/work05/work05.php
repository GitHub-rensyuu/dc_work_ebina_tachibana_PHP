<?php
$num =rand(0,100);
print '①番号は' . $num . 'です。';
if ($num % 6 === 0){
  print '3と6の倍数です。';
  } elseif($num % 3 === 0){
  print '3の倍数で、6の倍数ではありません。';
  } else {
  print '倍数ではありません。';
}
print '<br>';

$random01 =rand(1,10);
$random02 =rand(1,10);
print "②random01 = {$random01}, random02 = {$random02}です。";
if ($random01 > $random02){
  print "random01の方が大きいです。";
  } elseif ($random02 > $random01){
  print "random02の方が大きいです。";
  } else {
  print "2つは同じ数です。";
}
if ($random01 % 3 === 0 && $random02 % 3 === 0){
  print "2つの数字の中には3の倍数が2つ含まれています。";
  } elseif ($random01 % 3 !== 0 && $random02 % 3 !== 0){
  print "2つの数字の中に3の倍数が含まれていません。";
  } else {
  print "2つの数字の中には3の倍数が1つ含まれています。";
}

?>