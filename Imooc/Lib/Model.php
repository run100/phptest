<?php
/**
 * User: Run
 * Date: 下午4:14 15-4-14
 * File: Model.php
 * Desc:
 */

namespace Imooc\Lib;

abstract class Model
{
    protected $observers = array();

    function __construct()
    {
        //Imooc\App\Model\User
        $model_name =  str_replace('Imooc\App\Model\\', '', get_class($this));
        $model_name = strtolower($model_name);

        //提取配置
        $observers = Application::getInstance()->config['model'][$model_name]['observers'];
        if (!empty($observers)) {
            foreach($observers AS $class)
            {
                $this->observers[] = new $class;
            }
        }


    }

    public function notify($event)
    {

        foreach($this->observers AS $observer){
            $observer->update($event);
        }
    }
}