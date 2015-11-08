<?php


class person
{
  public $name;
  public $gender;

  public function say()
  {
    echo join(',', array($this->name, $this->gender)), PHP_EOL;
  }

  public function set($name, $value)
  {
    $this->name = $value;
  }

  public function get($name)
  {
    return $this->$name;
  }
}

$p = new person();
$p->name = 'zzw';
$p->gender = 'boy';
$p->say();


print_r(get_object_vars($p));
echo PHP_EOL;
print_r(get_class_vars(get_class($p)));
echo PHP_EOL;
print_r( get_class_methods(get_class($p)) );
echo PHP_EOL;
echo get_class($p);
echo PHP_EOL;

$reflect = new ReflectionObject($p);
$props = $reflect->getProPerties();
foreach ( $props as $prop) {
  echo $prop->getName(), PHP_EOL;
}


$m = $reflect->getMethods();
foreach ( $m as $prop) {
  echo $prop->getName(), PHP_EOL;
}
