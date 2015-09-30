<?php

define('DB', 'comment');


class MongoCli
{
  protected static $db;
  protected static $connection;

  public static function getConn()
  {
    if ( !self::$connection ) {
      self::$connection = new MongoClient("mongodb://mongo:27017");
    }

    return self::$connection;
  }

  public static function getDb()
  {
    if ( !self::$db ) {
      self::$db = self::getConn()->selectDb(DB);
    }

    return self::$db;
  }

  public static function getCollection($name)
  {
    return self::getDb()->selectCollection($name);
  }

  public static function dropCollection($name){
    return self::getCollection($name)->drop();
  }
}


//MongoCli::getDb();

