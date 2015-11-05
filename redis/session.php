<?php

if(!defined('REDIS_HOST')){
    define('REDIS_HOST', '127.0.0.1');
    define('REDIS_PORT', '6379');
}


class sessRedisCls
{
    static $redisHandle;

    public static function getRedisConn($alive_check = 0){
        static $redis;

        if($redis){
            if(!$alive_check){
                return $redis;
            }

            try{
                if('+PONG' == $redis->ping()){
                    return $redis;
                }
            }catch(RedisException $e){
                die($e->getMessage());
            }

        }

        $redis = new Redis();
        $redis->connect(REDIS_HOST, REDIS_PORT);

        return $redis;
    }

    public function getKey($id){
        return 'session_redis_'.$id;
    }


    public function __construct(){}

    public function open($path, $name){

        if(is_resource(self::$redisHandle)){
            return self::$redisHandle;
        }


        self::$redisHandle = self::getRedisConn(false);
        if(!self::$redisHandle){
            return false;
        }

        return true;
    }

    public function close(){
        #return true;
        if(self::$redisHandle){
            return self::$redisHandle->close();
        }
        return false;
    }

    public function read($id){
        $i = 10;
        //$redis = self::getRedisConn(false);
        $key = $this->getKey($id);

        while($i-- > 0){
            try{
                return self::$redisHandle->get($key);
            }catch(RedisException $e){
                usleep(50000);
                self::$redisHandle = self::getRedisConn(true);
            }

        }
    }

    public function write($id, $data){
        $i = 10;
        #$redis = self::getRedisConn(false);

        #尝试写入10次
        while($i-- > 0){
            try{
                $key = self::getKey($id);
                self::$redisHandle->set($key, $data);
                self::$redisHandle->expire($key, ini_get('session.gc_maxlifetime'));
                return;
            }catch (RedisException $e){
                usleep(5000);
                # 尝试重新链接
                self::$redisHandle = self::getRedisConn(true);
            }
        }
    }


    public function destory($id){
        $i = 10;
        #$redis = self::getRedisConn(false);

        while($i -- > 0){
            try{
                self::$redisHandle->delete($this->getKey($id));
                return;
            }catch (RedisException $e){
                usleep(50000);
                self::$redisHandle = self::getConnection(true);
            }
        }
    }

    public function gc($lifetime){
        $i = 10;
        #$redis = self::getRedisConn(false);

        try{
            self::$redisHandle->keys('session_redis*');

        }catch(RedisException $e){
            usleep(50000);
            self::$redisHandle = self::getRedisConn(true);

        }
        return true;
    }

    public static  function  register(){
        $session = new self();

        session_set_save_handler(
            array($session, 'open'),
            array($session, 'close'),
            array($session, 'read'),
            array($session, 'write'),
            array($session, 'destory'),
            array($session, 'gc')
        );
    }

	
}
