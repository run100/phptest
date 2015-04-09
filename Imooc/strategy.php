<?php
/**
 * User: Run
 * Date: 上午7:02 15-4-10
 * File: strategy.php
 * Desc: 策略模式
 */

require_once dirname(__FILE__).'/autoload.php';

class Page
{
    /**
     * @var \Imooc\Lib\Strategy
     */
    var $strategy = null;

    public function index(){
        echo "AD:";
        $this->strategy->showAd();

        echo "\r\nCategory:";
        $this->strategy->showCategory();

        echo "\r\n";
    }

    public function setStrategy(\Imooc\Lib\UserStrategy $stra){
        $this->strategy = $stra;
    }
}

$page = new Page();
if($argv[1] == 'male'){
    $strategy = new Imooc\Lib\Strategy\MaleUserStrategy();
}else{
    $strategy = new Imooc\Lib\Strategy\FemaleUserStrategy();
}

$page->setStrategy($strategy);
$page->index();