<?php
/**
 * User: Run
 * Date: 下午3:57 15-4-14
 * File: Json.php
 * Desc: 
 */

namespace Imooc\App\Decorator;

class Json
{
    /**
     * @var \Imooc\Lib\Controller
     */
    protected $controller;

    public function beforeRequest($controller)
    {
        $this->controller = $controller;
    }

    public function afterRequest($return_value)
    {
        printf("%s", json_encode($return_value));
        exit;
    }
}