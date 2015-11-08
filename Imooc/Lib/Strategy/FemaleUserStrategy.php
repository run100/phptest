<?php
/**
 * User: Run
 * Date: 上午7:05 15-4-10
 * File: FemaleUserStrategy.php
 * Desc: 
 */

namespace Imooc\Lib\Strategy;

use Imooc\Lib\UserStrategy;

class FemaleUserStrategy implements UserStrategy
{
    public function showAd()
    {
        echo '淘宝双11女装';
    }

    public function showCategory()
    {
        echo '女装大衣';
    }
}