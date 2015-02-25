<?php

$redis = new Redis();

$redis->connect('127.0.0.1', '6379');

#$redis->set('name', 'redis_session');
echo $redis->get('name');
#var_dump($redis);
