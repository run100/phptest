<?php
/**
 * User: Run
 * Date: 上午6:31 15-4-10
 * File: Pdo.php
 * Desc: 
 */

namespace Imooc\Lib\Database;

use Imooc\Lib\IDatabase;


class Pdo implements IDatabase
{
    /**
     * @var \PDO
     */
    protected $conn;

    public function conn($host, $user, $password, $dbname){
        $this->conn = new \PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    }

    public function query($sql){
        return $this->conn->query($sql);
    }

    public function close(){
        unset($this->conn);
    }
}