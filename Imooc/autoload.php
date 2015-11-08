<?php
/**
 * User: Run
 * Date: 上午7:02 15-4-10
 * File: autoload.php
 * Desc: 
 */

define('BASEDIR', substr(__DIR__,0,-6).'/');


include BASEDIR.'Imooc/Lib/Loader.php';

spl_autoload_register("\\Imooc\\Lib\\Loader::autoload");