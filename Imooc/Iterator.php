<?php
/**
 * User: Run
 * Date: 下午12:09 15-4-11
 * File: Iterator.php
 * Desc: 
 */

require_once dirname(__FILE__).'/autoload.php';

$users = new Imooc\Lib\AllUser();
foreach($users AS $user){
    var_dump($user);
}