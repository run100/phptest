<?php
/**
 * User: Run
 * Date: 上午6:42 15-4-10
 * File: IDatabase.php
 * Desc: 
 */


namespace Imooc\Lib;

interface IDatabase
{
    function conn($host, $user, $password, $dbname);
    function query($sql);
    function close();
}