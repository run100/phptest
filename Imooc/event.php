<?php
/**
 * User: Run
 * Date: 上午8:58 15-4-11
 * File: event.php
 * Desc: 
 */

require_once dirname(__FILE__).'/autoload.php';

class Event
{
    public function trigger(){
        echo "逻辑1";
        echo "\r\n";

        echo "逻辑2";
        echo "\r\n";

        echo "逻辑3";
        echo "\r\n";
    }
}

$event = new Event();
$event->trigger();