<?php
/**
 * User: Run
 * Date: 上午9:26 15-4-12
 * File: Application.php
 * Desc: 
 */

namespace Imooc\Lib;

class Application
{


    protected static $instance;

    public $config;

    protected function __construct()
    {
        $this->config = new Config();
        //print_r($this->config['controller']);
    }

    public static  function getInstance(){
        if(empty(self::$instance)){
            self::$instance = new self();
        }

        return self::$instance;
    }
}