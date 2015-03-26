<?php
/**
 * User: Run
 * Date: 下午3:33 15-3-25
 * File: mongoCls.php
 * Desc: 
 */

define('MongoDsn', 'localhost:27017');
define('MongoDb', 'run');

class MongoCls
{
    protected static $db;
    protected static $connection;

    /**
     * @return Mongo
     */
    public static function getConnection(){
        if(!self::$connection){
            //self::$connection = new Mongo(MongoDsn);
            self::$connection = new MongoClient();
            //new MongoClient()
        }

        return self::$connection;
    }

    /**
     * @return Mongo
     */
    public static  function reconnect(){
        if(!self::$connection){
            self::$connection->close();
            self::$connection = null;
        }

        return self::getConnection();
    }

    /**
     * @return MongoDB
     */
    public static function getDB(){
        if(!self::$db){
            self::$db = self::getConnection()->selectDB(MongoDb);
        }

        return self::$db;
    }

    /**
     * 根据名称获取集合
     * @param $name
     * @return MongoCollection
     */
    public static function getCollection($name){
        return self::getDB()->selectCollection($name);
    }

    /**
     * A shortcut to drop a mongo collection
     * @param $name
     * @return mixed
     */
    public static function dropCollection($name){
        return self::getConnection($name)->drop();
    }
}