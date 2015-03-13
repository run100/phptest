#!/usr/bin/php
<?php

$redis = new Redis();

$redis->connect('127.0.0.1', '6379');

#$redis->set('name', 'redis_session');
#echo $redis->get('name');
#var_dump($redis);
$redis->flushDB();
$rkey = 'mkey';
#$redis->set($rkey, uniqid('----'));

#echo $redis->exists($rkey).PHP_EOL;

#print $redis->get($rkey);
#$redis->sAdd($rkey, [1, 2, 3]);

#print_r($redis->randomKey());
#print_r( $redis->get($rkey) )."\r\n";

$tmpkey = 'intkey';
#$redis->set($tmpkey, 0);
$redis->incr($tmpkey);

echo $redis->get($tmpkey)."\r\n";

#print_r($redis->ping());