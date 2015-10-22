<?php

class Strcls
{
  var $str;
  public function __construct($str)
  {
    $this->str = $str;
  }

  public function __call($name, $arg)
  {
    print_r($name);
    print_r($arg);
  }


}

$str = new Strcls(' adsfa ');
$str->trim();
