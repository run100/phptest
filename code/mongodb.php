<?php
class LsMongo
{
  protected static $db;
  protected static $connection;

  /**
   * @return Mongo
   */
  public static function getConnection()
  {
    if (!self::$connection) {
      self::$connection = new Mongo(sfConfig::get('mongo_dsn'));

      //self::$connection = new Mongo(sfConfig::get('mongo_dsn'), array('replicaSet' => 'wanjia'));
      //self::$connection->setSlaveOkay(true);
    }
    return self::$connection;
  }

  public static function reconnect()
  {
    if (self::$connection) {
      self::$connection->close();
      self::$connection = null;
    }
    return self::getConnection();
  }
  /**
   * @return MongoDB
   */
  public static function getDB()
  {
    if (!self::$db) {
      self::$db = self::getConnection()->selectDB(sfConfig::get('mongo_database'));
    }
    return self::$db;
  }
  
  /**
   * Get a mongo collection by name
   * @param string $name
   * @return MongoCollection
   */
  public static function getCollection($name)
  {
    return self::getDB()->selectCollection($name);
  }
  
  /**
   * A shortcut to drop a mongo collection
   * @param string $name
   * @return array
   */
  public static function dropCollection($name)
  {
    return self::getCollection($name)->drop();
  }
}

