<?php
/**
 * User: Run
 * Date: 下午6:41 15-4-14
 * File: model.php
 * Desc: 
 */

$config = array(
    'user' => array(
        'observers' => array(
            'Imooc\App\Observer\Useradd1',
            'Imooc\App\Observer\Useradd2'
        )
    )
);


return $config;