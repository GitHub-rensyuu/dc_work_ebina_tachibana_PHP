<?php

      // 連想配列作成
    function randomScore() {
      return floor(rand(1,100));
    }

    $class01_member = [
      'tokugawa',
      'oda',
      'toyotomi',
      'takeda'
    ];

    $class01_score = [
      randomScore(),
      randomScore(),
      randomScore(),
      randomScore()
    ];

    
    $class02_member = [
      'minamoto',
      'taira',
      'sugawara',
      'fujiwara'
    ];

    $class02_score = [
      randomScore(),
      randomScore(),
      randomScore(),
      randomScore()
    ];
    
    // 配列class01とclass02を作成
    $class01 = array($class01_member, $class01_score);
    $class02 = array($class02_member, $class02_score);

    print 'class01連想配列：' . print_r($class01) . '<br>';
    print 'class02連想配列：' . print_r($class02) . '<br>';

    // class01とclass02をschoolという配列に格納
    $school = array($class01, $class02);


    // class01のodaさんの点数と、class02のsugawaraさんの点数を比較(値はschool配列から取得)
    print 'odaの点数は' . $school[0][1][1] . 'です。sugawaraの点数は' . $school[1][1][2] . 'です。<br>';
    if ($school[0][1][1] > $school[1][1][2]){
      print 'odaの点数の方が高いです。<br>';
    } else if ($school[1][1][2] > $school[0][1][1]) {
      print 'sugawaraの点数の方が高いです。<br>';
    } else {
      print '二人の点数は同じです。<br>';
    }

    // class01・class02それぞれの平均点を出力(値はschool配列から取得)
    $sum01 = array_sum($school[0][1]) / count($school[0][1]);
    $sum02 = array_sum($school[1][1]) / count($school[1][1]);
    print 'class01の平均点数：' . $sum01 . '<br>';
    print 'class02の平均点数：' . $sum02 . '<br>';

?>