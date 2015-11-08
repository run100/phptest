<?php
/**
 * User: Run
 * Date: 上午7:23 15-4-11
 * File: Mapping.php
 * Desc: 
 */

namespace Imooc\Lib;


class Mapping
{
    public $id;
    public $email;
    public $password;
    public $age;

    protected $db;

    public function __construct($id){
        $this->db = new \Imooc\Lib\Database\Mysqli();
        $this->db->conn('127.0.0.1','root','', 'lucene');
        $res = $this->db->query("SELECT * FROM user LIMIT 1");
        $data = $res->fetch_assoc();
        //var_dump($data);
        $this->id = $data['id'];
        $this->email = $data['email'];
    }

    public function __destruct(){
        $sql = "UPDATE `user` SET `email`='{$this->email}' , `password`='{$this->password}' WHERE id='{$this->id}'";
        echo $sql;
        $this->db->query($sql);

    }
}