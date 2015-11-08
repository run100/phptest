<?php
/**
 * User: Run
 * Date: 下午2:50 15-4-14
 * File: Template.php
 * Desc:
 */

namespace Imooc\App\Decorator;


class Template
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
        foreach ($return_value AS $key => $val) {
            $this->controller->assign($key, $val);
        }

        $this->controller->display();
    }
}
