<?php
/**
 * User: Run
 * Date: 8:57 2015/11/5
 * File: captcha.php
 * Desc:
 */

header('Content-Type: image/png');
$img = imagecreatetruecolor(150, 30) or die('gd2 error');

// var_dump($image);
$text_color = imagecolorallocate($img, 255, 255, 255);
$bgcolor = imagecolorallocate($img, 0, 0, 0);

//var_dump($text_color);
imagefill($img, 0, 0, $bgcolor);
imagestring($img, 2, 5, 5, 'a simple text string', $text_color);

imagepng($img);

imagedestroy($img);