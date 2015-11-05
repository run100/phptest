<?php
/**
 * User: Run
 * Date: 上午6:15 15-4-10
 * File: Register.php
 * Desc: 
 */

namespace Imooc\Lib;

class Register
{
    protected static  $objects;

    static function set($alias, $object){
        self::$objects[$alias] = $object;
    }

    static function get($alias){
        if( isset(self::$objects[$alias]) ){
            return self::$objects[$alias];
        }

    }

    function _unset($alias){
        unset(self::$objects[$alias]);
    }
}