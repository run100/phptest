<?php

class person
{
  var $name;
  var $gender;

  public function say()
  {
      echo 'name is '.$this->name.' gender is '.$this->gender;
      echo "\r\n";
  }
}

class family
{
  public $people;
  public $location;
  public function __construct($p, $loc)
  {
    $this->people = $p;
    $this->location = $loc;
  }
}



$student = new person();
$student->name = 'tom';
$student->gender = 'male';
$student->say();

$family = new family($student, 'hefei');
$family->people->say();

echo "\n";
echo serialize($student);

$stuarr = array('name'=>'1', 'age'=>1);
echo "\n";
echo serialize($stuarr);

echo "\n";
echo serialize($family);

echo "\n";
/*print_r((array)$student);
echo "\r\n";
var_dump( $student );*/

