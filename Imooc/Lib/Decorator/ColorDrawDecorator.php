<?php
/**
 * User: Run
 * Date: 上午11:15 15-4-11
 * File: ColorDrawDecorator.php
 * Desc: 
 */

namespace Imooc\Lib\Decorator;

class ColorDrawDecorator implements DrawDecorator
{
    protected $color;
    public function __construct($color = 'red'){
        $this->color = $color;
    }

    function beforeDraw(){
        echo "<div style='color:{$this->color};'>";

    }

    function afterDraw(){
        echo '</div>';
    }
}