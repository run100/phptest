<?php
/**
 * User: Run
 * Date: 上午7:06 15-4-10
 * File: MaleUserStrategy.php
 * Desc: 
 */


namespace Imooc\Lib\Strategy;

use Imooc\Lib\UserStrategy;

class MaleUserStrategy implements UserStrategy
{
    public function showAd()
    {
        echo '男式IPhone6 Plus';
    }

    public function showCategory()
    {
        echo '男式笔记本';
    }
}