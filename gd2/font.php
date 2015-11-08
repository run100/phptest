<?php
/**
 * User: Run
 * Date: 11:17 2015/11/5
 * File: font.php
 * Desc:
 */
header('Content-Type: text/html; charset=utf-8');

// 新建画布
$im = imagecreatetruecolor(100, 30);

// 背景色
$bgcolor = imagecolorallocate($im, 247, 247, 247);

// 填充
imagefill($im, 0, 0, $bgcolor);

$fontface = 'simhei.ttf';

$str='天地不仁以万物为刍狗圣人不仁以百姓为刍狗这句经常出现在控诉暴君暴政上地残暴不仁把万物都当成低贱的猪狗来看待而那些高高在上的所谓圣人们也没两样还不是把我们老百姓也当成猪狗不如的东西但实在正取的解读是地不情感用事对万物一视同仁圣人不情感用事对百姓一视同仁执子之手与子偕老当男女主人公含情脉脉看着对方说了句执子之手与子偕老女方泪眼朦胧含羞地回一句讨厌啦这样的情节我们是不是见过很多但是我们来看看这句的原句死生契阔与子成说执子之手与子偕老于嗟阔兮不我活兮于嗟洵兮不我信兮意思是说战士之间的约定说要一起死现在和我约定的人都走了我怎么活啊赤裸裸的兄弟江湖战友友谊啊形容好基友的基情比男女之间的爱情要合适很多吧';

$fontarr = str_split($str, 3);

for ( $i=0; $i<4; $i++ ) {
  $fontcolor = imagecolorallocate($im, rand(0, 120), rand(0, 120), rand(0, 120));
  $cn = $fontarr[ rand(0, count($fontarr)) ];
  /*imagettftext (resource $image ,float $size ,float $angle ,int $x ,int $y,int $color,
        string $fontfile ,string $text ) 幕布 ，尺寸，角度，坐标，颜色，字体路径，文本字符串
        mt_rand()生成更好的随机数,比rand()快四倍*/
  imagettftext($im, 10, mt_rand(-10, 10), (20*$i+2), mt_rand(10, 25)
      , $fontcolor, $fontface, $cn);
}

header('Content-Type: image/png');
imagepng($im);
imagedestroy($im);

//imagettftext($im, )