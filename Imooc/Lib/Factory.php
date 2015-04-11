<?php
/**
 * User: Run
 * Date: 上午7:07 15-4-9
 * File: Factory.php
 * Desc: 
 */


namespace Imooc\Lib;

class Factory
{
    static function createDatabase()
    {
        $db = Database::getInstance();
        Register::set('db1', $db);
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