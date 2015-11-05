<?php
/**
 * User: Run
 * Date: 上午6:31 15-4-10
 * File: Mysql.php
 * Desc: 
 */

namespace Imooc\Lib\Database;

use Imooc\Lib\IDatabase;


class Mysql implements IDatabase
{
    #static $conn = null;
    protected $conn;

    public function conn($host, $user, $password, $dbname){
        $this->conn = mysql_connect($host, $user, $password) or die('conn die');
        mysql_query("SET NAMES utf8");
        mysql_select_db($dbname, $this->conn);
    }

    public function query($sql){
        $res = mysql_query($sql, $this->conn);
        return $res;
    }

    public function close(){
        mysql_close($this->conn);
        return true;
    }
}