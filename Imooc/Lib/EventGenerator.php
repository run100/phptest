<?php
/**
 * User: Run
 * Date: 上午9:11 15-4-11
 * File: Event.php
 * Desc: 
 */

namespace Imooc\Lib;

abstract class EventGenerator
{
    private $observers = array();
    
    public function addObserver(Observer $observer){
        $this->observers[] = $observer;
    }

    public function notity(){

    }
}