<?php
/**
 * User: Run
 * Date: 上午7:21 15-4-11
 * File: dbmap.php
 * Desc: 
 */


require_once dirname(__FILE__).'/autoload.php';


/*$map = new Imooc\Lib\Mapping(1);

var_dump($map->id,$map->email);
#echo $map->email;

$map->email = 'test4444@163.com';
$map->password = 'test';*/

class Page
{
    public function index(){
        $user = Imooc\Lib\Factory::getMapping(1);
        $user->email = '111@163.com';
        $this->test();
        echo "OK";
    }

    public function test(){
        $user = Imooc\Lib\Factory::getMapping(1);
        $user->password = '222';
    }
}

$page = new Page();
$page->index();
