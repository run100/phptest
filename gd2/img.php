<?php
/**
 * User: Run
 * Date: 15:58 2015/11/5
 * File: img.php
 * Desc: php图像处理函数大全(缩放、剪裁、缩放、翻转、旋转、透明、锐化的实例总结)
 */

$file = 'a.jpg';
$img = imagecreatefromjpeg($file);
$percent = '0.5';

list($w, $h) = getimagesize($file);
/*$nw = $w * $percent;
$nh = $w * $percent;*/
$nw = 500;
$nh = floor( ($nw/$w)*$h  );

// 创建新画布
$img_p = imagecreatetruecolor($nw, $nh);

// 重采样拷贝部分图像并调整大小
imagecopyresampled($img_p, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
// 拷贝部分图像并调整大小
//imagecopyresized($img_p, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);

header('Content-Type: image/jpeg');
imagejpeg($img_p, null, 100);
imagedestroy($img_p);