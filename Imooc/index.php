<?php
/**
 * User: Run
 * Date: 上午6:05 15-4-9
 * File: index.php
 * Desc: 
 */

define('BASEDIR', substr(__DIR__,0,-6).'/');


include BASEDIR.'Imooc/Lib/Loader.php';

spl_autoload_register("\\Imooc\\Lib\\Loader::autoload");

// 工厂模式
$db = Imooc\Lib\Factory::createDatabase();
$db('param');

//Imooc\Lib\Object::test();
//Imooc\App\Controller\Home\Index::test();

// 魔术方法测试
//$db = new Imooc\Lib\Database();
//$db->test('hello', 123);
// Imooc\Lib\Database::test("aaa");
//echo $db;
//$db('param');
