<?php
/**
 * User: Run
 * Date: 下午8:22 15-4-13
 * File: Home.php
 * Desc:
 */

namespace Imooc\App\Controller;

use Imooc\Lib\Controller;


class Home extends Controller
{


    public function index()
    {
        echo __NAMESPACE__ . ' static function test <br>';
        return array('uid' => 1, 'username' => 'chromev');
    }
}