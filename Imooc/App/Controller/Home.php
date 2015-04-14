<?php
/**
 * User: Run
 * Date: 下午8:22 15-4-13
 * File: Home.php
 * Desc:
 */

namespace Imooc\App\Controller;

use Imooc\Lib\Controller;
use Imooc\Lib\Factory;


class Home extends Controller
{


    public function index()
    {
        echo __NAMESPACE__ . ' static function test <br>';
        //return array('uid' => 1, 'username' => 'chromev');
        $model = Factory::getModel('user');

        $model->create(array('email'=>'bb@163.com','age'=>'20'));

        $info = $model->getInfo(1);


        return $info;
    }

    public function index2()
    {
        //echo uniqid('---');
        $db = Factory::getDatabase();
        $db->query("select * from user");
        $db->query("delete from user where id=1");
        $db->query("update user set name='rango2' where id=1");
        return array();
    }
}