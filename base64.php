<?php
/*
$string = 'http%3A%2F%2Flife.zzw.365jia.lab%2Fservices%2Fbusline%2Fldetail%2Fid%2F283.html';

$encode =  base64_encode($string);

echo $encode."\r\n";

echo  base64_decode($encode);


$string = '';
echo base64_decode($string);

*/

//echo mb_strlen('11中国','utf-8');

$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);

print_r($fp);
