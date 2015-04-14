<?php
/**
 * User: Run
 * Date: 下午9:43 15-4-14
 * File: Proxy.php
 * Desc: 
 */

namespace Imooc\Lib\Database;

use Imooc\Lib\Factory;

class Proxy
{
    function query($sql)
    {
        if( substr($sql, 0, 6) == 'select' )
        {
            printf("读操作%s<BR/>",$sql);
            return Factory::getDatabase('slave')->query($sql);
        }
        else
        {
            printf("写操作%s<BR/>",$sql);
            return Factory::getDatabase('master')->query($sql);
        }
    }
}