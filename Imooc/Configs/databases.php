<?php
/**
 * User: Run
 * Date: 上午9:50 15-4-12
 * File: databases.php
 * Desc: 
 */

$config = array(
    'master' => array(
        'type' => 'MySQL',
        'host' => '127.0.0.1',
        'user' => 'root',
        'password' => '',
        'dbname' => 'lucene',
    ),
    'slave' => array(
        'slave1' => array(
            'type' => 'MySQL',
            'host' => '127.0.0.1',
            'user' => 'root',
            'password' => '',
            'dbname' => 'lucene',
        ),
        'slave2' => array(
            'type' => 'MySQL',
            'host' => '127.0.0.1',
            'user' => 'root',
            'password' => '',
            'dbname' => 'lucene',
        ),
    ),
);