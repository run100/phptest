<?php
/**
 * User: Run
 * Date: 上午11:31 15-4-11
 * File: SizeDrawDecorator.php
 * Desc: 
 */

namespace Imooc\Lib\Decorator;

class SizeDrawDecorator implements DrawDecorator
{
    protected $size;
    public function __construct($size = '14px'){
        $this->size = $size;
    }

    function beforeDraw(){
        echo "<div style='font-size:{$this->size};'>";

    }

    function afterDraw(){
        echo '</div>';
    }
}