<?php
/**
 * User: Run
 * Date: 下午4:57 15-3-22
 * File: q2.php
 * Desc: 
 */


define('REDIS_HOST', '127.0.0.1');
define('REDIS_PORT', '6379');

$redisCls = new Redis();
$redisCls->connect(REDIS_HOST, REDIS_PORT);

$key = 'queue';
"lLen".var_dump($redisCls->lLen($key));
echo "\r\n";
"lRange".var_dump($redisCls->lRange($key, 30, 40));
echo "\r\n";
"lIndex".var_dump($redisCls->lIndex($key, 2));
echo "\r\n";
"lSize".var_dump($redisCls->lSize($key));
echo "\r\n";

