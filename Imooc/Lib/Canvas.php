<?php
/**
 * User: Run
 * Date: 上午10:37 15-4-11
 * File: Canvas.php
 * Desc: 
 */

namespace Imooc\Lib;

use Imooc\Lib\Decorator\DrawDecorator;

class Canvas
{
    var $data = null;
    var $decorators = array();

    //Decorator
    function init($width = 20, $height = 10)
    {
        $data = array();
        for($i = 0; $i < $height; $i++)
        {
            for($j = 0; $j < $width; $j++)
            {
                $data[$i][$j] = '*';
            }
        }
        $this->data = $data;
    }

    public function addDecorators(DrawDecorator $drawdecorator)
    {
        //var_dump($drawdecorator);
        $this->decorators[] = $drawdecorator;
    }

    public function beforeDraw()
    {
        if(is_array($this->decorators)){
            foreach($this->decorators AS $decorator){
                $decorator->beforeDraw();
            }
        }
    }

    public function afterDraw()
    {
        if(is_array($this->decorators)){
            $decorators = array_reverse($this->decorators);
            foreach($decorators AS $decorator){
                $decorator->afterDraw();
            }
        }
    }

    function draw()
    {
        $this->beforeDraw();
        foreach($this->data as $line)
        {
            foreach($line as $char)
            {
                echo $char;
            }
            echo "<br />\n";
        }
        $this->afterDraw();
    }

    function rect($a1, $a2, $b1, $b2)
    {
        foreach($this->data as $k1 => $line)
        {
            if ($k1 < $a1 or $k1 > $a2) continue;
            foreach($line as $k2 => $char)
            {
                if ($k2 < $b1 or $k2 > $b2) continue;
                $this->data[$k1][$k2] = '&nbsp;';
            }
        }
    }
}