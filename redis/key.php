#!/usr/bin/php
<?php
/**
 * User: Run
 * Date: 下午2:58 15-3-21
 * File: key.php
 * Desc: 
 */

define('REDIS_HOST', '127.0.0.1');
define('REDIS_PORT', '6379');

/*$redis = new Redis();
$redis->connect(REDIS_HOST, REDIS_PORT);
if(!$redis->ping() == '+PONG'){
    exit('redis ping is error,check');
}*/



#echo "getOption".$redis->getOption().$rn;

class redisClass
{
    protected static $redis;

    var $rn = "\r\n";

    public static function getConn($alive_check = 0)
    {
        if(self::$redis){
            if(!$alive_check){
                return self::$redis;
            }

            try{
                if(self::$redis->ping() == '+PONG'){
                    return self::$redis;
                }
            }catch (RedisException $e){
                print_r($e->getMessage());
            }
        }

        self::$redis = new Redis();
        self::$redis->connect(REDIS_HOST, REDIS_PORT);
        return self::$redis;
    }

    public function testCmd($cmd, $param)
    {
        echo "$cmd ".self::$redis->$cmd.$this->rn;
    }
}

$redisCls = redisClass::getConn();
$pagkey = 'page_size';
$redisCls->set($pagkey, 10);
var_dump($redisCls->incr($pagkey));
echo "\r\n";
var_dump($redisCls->get($pagkey));
echo "\r\n";
var_dump($redisCls->decr($pagkey));
echo "\r\n";
var_dump($redisCls->get($pagkey));
echo "\r\n";
#hset

/*$key = 'runing2008';
$redisCls->hSet($key,'guanzhu', 25);
$redisCls->hSet($key,'fans', 3769);
$redisCls->hSet($key,'microblogs', 418);*/

/*
print_r($redisCls->hGet($key, 'guanzhu'));
var_dump($redisCls->hExists($key,'guanzhu'));
var_dump($redisCls->hExists($key,'fans1'));
print_r($redisCls->hKeys($key));
print_r($redisCls->hVals($key));
print_r($redisCls->hLen($key));
print_r($redisCls->hGetAll($key));*/

/*
#集合
$memkey = 'mems';
$redisCls->sAdd($memkey, 1000, 2000);
$redisCls->sAdd($memkey, 6666, 88888);
var_dump($redisCls->sRemove($memkey, 88888));
print_r($redisCls->sMembers($memkey));
print_r($redisCls->sSize($memkey));
var_dump($redisCls->sContains($memkey, 3333));*/


#列表


echo "\r\n";

#echo $redisCls->get('session_redis_o5454u6sn4ohhja22vl31m7ru0');
#echo "\r\n";

#echo $redisCls->getOption(Redis::OPT_SERIALIZER);
#print_r($redisCls->time()   ) ;
#print_r($redisCls->slowlog('get', 10));
#print_r($redisCls->slowlog('get'));
#var_dump($redisCls->dbSize());
#echo "\r\n";
#var_dump($redisCls->config('databases', 'get'));

#print_r($redisCls->config('get', 'databases'));
#$redisCls->select(0);
#$redisCls->flushAll();
#print_r($redisCls->keys('*'));

/*
$redisCls->flushdb();

$keyname = 'myname';
#$redisCls->set($keyname, 'run');

$redisCls->expire($keyname, 30);

var_dump($redisCls->get($keyname));
echo "\r\n";
$redisCls->ttl($keyname);
echo "\r\n";

#print_r($redisCls->info());
#echo "\r\n";




/*$array_mset_keys=array('one'=>'1',
    'two'=>'2',
    'three '=>'3',
    'four'=>'4');

$redisCls->mset($array_mset_keys);

var_dump($redisCls->keys('*o*'));

var_dump($redisCls->keys('*'));*/

/*$redisCls->set($keyname, 'run');
echo $redisCls->get($keyname)."\r\n";
var_dump($redisCls->del($keyname));
var_dump($redisCls->exists($keyname));

# 情况3： 同时删除多个key
$arr_mset = array('fkey'=>'fval', 'skey'=>'sval', 'tkey'=>'tval');
$redisCls->mset($arr_mset);

#var_dump($redis->get());
$arr_mget_key =  array('fkey','skey','tkey');
var_dump($redisCls->mget($arr_mget_key));

$redisCls->del($arr_mset);
var_dump($redisCls->mget($arr_mget_key));*/




