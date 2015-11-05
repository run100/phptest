<?php
/**
 * User: Run
 * Date: 上午6:31 15-4-10
 * File: Mysqli.php
 * Desc: 
 */


namespace Imooc\Lib\Database;

use Imooc\Lib\IDatabase;


class Mysqli implements IDatabase
{

    protected $conn;

    public function conn($host, $user, $password, $dbname){
        $this->conn = mysqli_connect($host, $user, $password, $dbname);
    }

    public function query($sql){
        return mysqli_query($this->conn, $sql);
    }

    public function close(){
        mysqli_close($this->conn);
    }
}