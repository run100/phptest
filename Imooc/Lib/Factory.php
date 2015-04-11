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