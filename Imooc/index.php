<?php
/**
 * User: Run
 * Date: 上午6:05 15-4-9
 * File: index.php
 * Desc: 
 */

define('BASEDIR', __DIR__);

include BASEDIR.'/Lib/Loader.php';

spl_autoload_register("\\Imooc\\Lib\\Loader::autoload");

Imooc\Lib\Object::test();
#Imooc\App\Controller\Home\Index::test();