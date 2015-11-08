<?php
/**
 * User: Run
 * Date: 上午6:11 15-4-9
 * File: Loader.php
 * Desc: 
 */


namespace Imooc\Lib;

class Loader
{
    static function autoload($class)
    {
        #var_dump($class);

        $class = BASEDIR.str_replace('\\', '/', $class).'.php';
        #echo $class;
        require_once($class);

    }
}