<?php
/**
 * User: Run
 * Date: 上午9:35 15-4-12
 * File: default.php
 * Desc:
 */

error_reporting(E_ALL);

require_once dirname(__FILE__) . '/autoload.php';

define('WEBDIR', BASEDIR . 'Imooc/');

\Imooc\Lib\Application::getInstance()->dispatch();
//$db = \Imooc\Lib\Factory::getDatabase('slave');
//var_dump($db);