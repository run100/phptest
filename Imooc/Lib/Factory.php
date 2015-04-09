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
}