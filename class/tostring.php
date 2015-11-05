<?php

class person
{
  var $name = 'person';
  var $gender;
  static $money = 1000;

  public function __construct()
  {
    echo "父类", PHP_EOL;
  }

  public function say()
  {
    echo $this->name .' ' . $this->gender, PHP_EOL;
  }
}

class family extends person
{
  public $name;
  public $gender;
  public $age;
  static $money = 999;

  public function __construct()
  {
    parent::__construct();
    echo "子类", PHP_EOL;
  }

  public function say()
  {
    echo self::$money, PHP_EOL;
    echo parent::$money, PHP_EOL;
  }
}

$f = new family();
$f->say();
echo $f::$money, PHP_EOL;

exit;

class Accout
{
  public $user = 'running';
  private $pwd = '2';

  public function __toString()
  {
    return $this->user.','.$this->pwd;
  }
}


$a = new Accout();
echo $a;
echo PHP_EOL;
$b = (array)$a;
echo $b['user'];
echo PHP_EOL;
