<?php
/**
 * Created by PhpStorm.
 * User: run
 * Date: 15/10/24
 * Time: 上午10:11
 */

namespace my\name;

class MyClass
{
  public function __construct()
  {
    echo __CLASS__, PHP_EOL;
  }
};
function myfunction(){};
const MYCONST = 1;

$a = new MyClass();
$c = new \my\name\MyClass();