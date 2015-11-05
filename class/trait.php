<?php


trait Hello
{
  public function sayHello()
  {
    echo 'Hello', PHP_EOL;
  }
}

trait World
{
  public function sayWorld()
  {
    echo 'World', PHP_EOL;
  }
}

class MyHelloWorld
{
  use Hello, World;
  public function sayMark()
  {
    echo '! ';
  }
}

$o = new MyHelloWorld();
$o->sayHello();
$o->sayWorld();
$o->sayMark();

