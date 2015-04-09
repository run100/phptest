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


// 选配器模式
/*//$db = new Imooc\Lib\Database\Pdo();
$db = new Imooc\Lib\Database\Mysql();
$db->conn('127.0.0.1', 'root', '', 'test');
$res = $db->query('show databases');
var_dump($res);
$db->close();*/

// 注册模式
/*Imooc\Lib\Database::createDatabase();
$db1 = Imooc\Lib\Register::get('db1');
var_dump($db1);*/

// 单例模式
/*$db = Imooc\Lib\Database::getInstance();
$db = Imooc\Lib\Database::getInstance();
$db = Imooc\Lib\Database::getInstance();
$db = Imooc\Lib\Database::getInstance();*/
// 工厂模式
//$db = Imooc\Lib\Factory::createDatabase();
//$db('param');

//Imooc\Lib\Object::test();
//Imooc\App\Controller\Home\Index::test();

// 魔术方法测试
//$db = new Imooc\Lib\Database();
//$db->test('hello', 123);
// Imooc\Lib\Database::test("aaa");
//echo $db;
//$db('param');
