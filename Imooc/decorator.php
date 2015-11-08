<?php
/**
 * User: Run
 * Date: 上午10:45 15-4-11
 * File: decorator.php
 * Desc: 
 */
error_reporting(E_ALL);

require_once dirname(__FILE__).'/autoload.php';

$canvas = new \Imooc\Lib\Canvas();

$canvas->init();
$canvas->addDecorators(new \Imooc\Lib\Decorator\ColorDrawDecorator('green'));
$canvas->addDecorators(new \Imooc\Lib\Decorator\SizeDrawDecorator('16px'));
$canvas->rect(3,4,6,19);
$canvas->draw();