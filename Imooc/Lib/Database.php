<?php
/**
 * User: Run
 * Date: 上午6:43 15-4-9
 * File: Database.php
 * Desc: 
 */

namespace Imooc\Lib;



class Database
{
    static  $db;

    protected  function __construct(){

    }

    public function getInstance()
    {

        self::$db  = !self::$db ? new self() : self::$db;

        return self::$db;
    }


    public function __call($func, $param){
        var_dump($func, $param);
        return "magic functionc\n";
    }

    static function __callStatic($func, $param){
        var_dump($func, $param);
        return "magic static functionc\n";
    }

    public function __toString(){
        return __CLASS__;
    }

    public function __invoke($param){
        var_dump($param);
        return "__invoke\n";
    }
}