<?php
/**
 * User: Run
 * Date: 上午7:07 15-4-9
 * File: Factory.php
 * Desc: 
 */


namespace Imooc\Lib;

use Imooc\Lib\Database\Mysqli;

class Factory
{
    static function getDatabase($id = 'master'){

        $key = 'database_'.$id;
        // 获取配置
        if($id == 'slave'){
            $slaves = Application::getInstance()->config['databases'][$id];
            $db_conf = $slaves[array_rand($slaves)];
        }else{
            $db_conf = Application::getInstance()->config['databases'][$id];
        }

        $db = Register::get($key);
        if(!$db){
            $db = new Database\Mysqli();
            $db->conn($db_conf['host'], $db_conf['user'], $db_conf['password'], $db_conf['dbname']);
            Register::set($key, $db);
        }
        return $db;
    }

    static function createDatabase($type)
    {
        $db = Register::get($type);

        if(!$db){
            //$db = Database::getInstance();
            $db = new Mysqli();
            $db->conn('127.0.0.1','root', '', 'lucene');
            Register::set($type, $db);
        }

        return $db;
    }

    /**
     * @param $id
     * @return Mapping
     */
    static function getMapping($id){
        $key = 'user_'.$id;
        $user = Register::get($key);
        if(!$user){
            $user = new Mapping($id);
            Register::set($key, $user);
        }

        return $user;
    }
}