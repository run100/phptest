<?php
/**
 * User: Run
 * Date: 下午8:34 15-4-13
 * File: Controller.php
 * Desc:
 */

namespace Imooc\Lib;

abstract class Controller
{
    protected $data;
    protected $controller_name;
    protected $view_name;
    protected $template_dir;

    public function __construct($controller_name, $view_name)
    {
        $this->controller_name = $controller_name;
        $this->view_name = $view_name;
        $this->template_dir = WEBDIR.'/templates';
    }


    public function assign($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function display($file = '')
    {

        if ( empty($file) ) {
            $file = strtolower($this->controller_name).'/'.$this->view_name.'.php';
        }
        //echo $file;

        $path = $this->template_dir.'/'.$file;
        extract($this->data);
        include $path;
    }

}