<?php
/**
 * User: Run
 * Date: 上午10:58 15-4-11
 * File: DrawDecorator.php
 * Desc: 
 */

namespace Imooc\Lib\Decorator;

interface DrawDecorator
{
    function beforeDraw();
    function afterDraw();
}