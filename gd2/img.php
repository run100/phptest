<?php
/**
 * User: Run
 * Date: 15:58 2015/11/5
 * File: img.php
 * Desc: phpͼ��������ȫ(���š����á����š���ת����ת��͸�����񻯵�ʵ���ܽ�)
 */

$file = 'a.jpg';
$img = imagecreatefromjpeg($file);
$percent = '0.5';

list($w, $h) = getimagesize($file);
/*$nw = $w * $percent;
$nh = $w * $percent;*/
$nw = 500;
$nh = floor( ($nw/$w)*$h  );

// �����»���
$img_p = imagecreatetruecolor($nw, $nh);

// �ز�����������ͼ�񲢵�����С
imagecopyresampled($img_p, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
// ��������ͼ�񲢵�����С
//imagecopyresized($img_p, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);

header('Content-Type: image/jpeg');
imagejpeg($img_p, null, 100);
imagedestroy($img_p);