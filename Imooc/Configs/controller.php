<?php
/**
 * User: Run
 * Date: 上午8:58 15-4-12
 * File: controller.php
 * Desc: 
 */

$config = array(
    'home' => array(
        'decorator'=>array(
            'Imooc\App\Decorator\Template'
        )
    ),
    'default' => 'Hello world'
);

return $config;