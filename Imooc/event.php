<?php
/**
 * User: Run
 * Date: 上午8:58 15-4-11
 * File: event.php
 * Desc: 
 */

require_once dirname(__FILE__).'/autoload.php';

class Event extends \Imooc\Lib\EventGenerator
{
    public function trigger(){
        /*echo "逻辑1";
        echo "\r\n";

        echo "逻辑2";
        echo "\r\n";

        echo "逻辑3";
        echo "\r\n";*/
        echo "Event\r\n";
        $this->notity();
    }
}

class Observer1 implements \Imooc\Lib\Observer
{
    function update($event_info = null){
        echo "逻辑1\r\n";
    }
}

$event = new Event();
$event->addObserver(new Observer1);
$event->trigger();