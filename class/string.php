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

    $this->str = call_user_func($name, $this->str);

    return $this;
  }

}

/*$str = new Strcls('adsfa dsf');
$str->trim()->strlen();
echo $str->str;*/

class Test3
{
  public static  $str = '';

  public function __construct($str)
  {
    self::$str = $str;
  }

  public static function __callStatic($name, $arg)
  {

    self::$str = call_user_func($name, $arg);
    return new self;
  }

  public function getVar()
  {
    echo self::$str;
  }
}

var $t = new Test3('asdfa asdf ');
$t::trim()::getVar();

class Test2
{
  public static $var = array();

  public static function setName($name)
  {
    self::$var['name'] = $name;
    return new self;
  }

  public static function getVar()
  {
    return self::$var;
  }
}

// print_r(Test2::setName('helloworld')->getVar());
