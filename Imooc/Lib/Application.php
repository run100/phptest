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

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    public function dispatch()
    {

        list($c, $v) = explode('/', trim(substr($_SERVER['REQUEST_URI'], 6), '/'));

        $c_low = strtolower($c);
        $c = ucwords($c);

        $class = '\\Imooc\\App\\Controller\\' . $c;
        $controller_config = $this->config['controller'];

        $decorators = array();
        if (isset($controller_config[$c_low]['decorator'])) {
            $conf_decorator = $controller_config[$c_low]['decorator'];
            foreach ($conf_decorator as $dclass) {
                $decorators[] = new $dclass;
            }
        }

        $obj = new $class($c, $v);
        foreach ($decorators as $decorator) {
            $decorator->beforeRequest($obj);
        }

        $return_value = $obj->$v();
        foreach ($decorators as $decorator) {
            $decorator->afterRequest($return_value);
        }

    }
}