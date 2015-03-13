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

$keyname = 'myname';

$redisCls->keys('*');
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




