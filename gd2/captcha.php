<?php
/**
 * User: Run
 * Date: 8:57 2015/11/5
 * File: captcha.php
 * Desc:
 */


$img = imagecreatetruecolor(100, 30) or die('gd2 error');

// var_dump($image);
$bgcolor = imagecolorallocate($img, 247, 247, 247);
imagefill($img, 0, 0, $bgcolor);

//var_dump($text_color);
/*
imagestring($img, 2, 5, 5, 'a simple text string', $text_color);*/

$fontsize = 6;

// 生成随机颜色
$captcha_code = '';
for ( $i=0; $i<4; $i++ ) {
  $fontcolor = imagecolorallocate($img, rand(0, 120), rand(0, 120), rand(0, 120));
  $fontcontent = rand(0, 9);
  $captcha_code .= $fontcontent;
  $x = ($i*100/4)+rand(5, 10);

  $y = rand(5, 10);
  imagestring($img, $fontsize, $x, $y, $fontcontent, $fontcolor);
}


for ( $j = 0; $j < 200; $j ++ ) {
  $pointcolor = imagecolorallocate($img, rand(5, 200), rand(5, 200), rand(5, 200));
  imagesetpixel($img, rand(0, 99), rand(0, 99), $pointcolor);
}

for ( $j = 0; $j < 2; $j ++ ) {
  $linecolor = imagecolorallocate($img, rand(80, 220), rand(80, 220), rand(80, 200));
  imageline($img, rand(1, 99), rand(2, 29), rand(40, 99), rand(3, 29), $linecolor);
}


header('Content-Type: image/png');
imagepng($img);
imagedestroy($img);