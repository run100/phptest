<?php


class Accout
{
  private $user = 1;
  private $pwd = 2;

  public function __call($name, $argv)
  {
    switch( count($argv) ){
      case 2:
          echo $argv[0],$argv[1], PHP_EOL;
        break;
      case 3:
          echo array_sum($argv[0], $argv[1], $argv[2]), PHP_EOL;
        break;
      default:
          print_r('error '.$name);
      break;
    }
  }

  public function __set($name, $value)
  {
    $this->$name = $value;
  }

  public function __get($name)
  {
    if ( ! isset($name) ) {
      echo '未设置';
      $this->$name = '设置默认值';
    }
    return $this->$name;
  }


}

$a = new Accout();
$a->user = 1;
echo $a->user;

$a->make(5);
$a->make(5, 6);
