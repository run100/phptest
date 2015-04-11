<?php
/**
 * User: Run
 * Date: 上午9:23 15-4-11
 * File: Observer.php
 * Desc: 
 */

namespace Imooc\Lib;


interface Observer
{
    public function update($event_info = null);
}