<?php
$num =rand(0,100);
print '①番号は' . $num . 'です。';
?>
<?php switch(true):
  case $num % 6 === 0: ?>
    <p>3と6の倍数です。</p>
  <?php
    break;
    case $num % 3 === 0:
  ?>
    <p>3の倍数で、6の倍数ではありません。</p>
  <?php break;
  default: ?>
    <p>倍数ではありません。</p>
<?php endswitch; ?>
<?php
print '<br>';
$random01 =rand(1,10);
$random02 =rand(1,10);
print "②random01 = {$random01}, random02 = {$random02}です。";
?>
<?php switch(true):
  case $random01 > $random02: ?>
    <p>random01の方が大きいです。</p>
  <?php break;
  case $random02 > $random01: ?>
    <p>random02の方が大きいです。</p>
  <?php break;
  default: ?>
    <p>2つは同 じ数です。</p>
<?php endswitch; ?>
<?php switch(true):
  case $random01 % 3 === 0 && $random02 % 3 === 0: ?>
    <p>2つの数字の中には3の倍数が2つ含まれています。</p>
  <?php break;
  case $random01 % 3 !== 0 && $random02 % 3 !== 0: ?>
    <p>2つの数字の中に3の倍数が含まれていません。</p>
  <?php break;
  default: ?>
    <p>2つの数字の中には3の倍数が1つ含まれています。</p>
<?php endswitch; ?>