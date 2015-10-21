<?php

class person {
	var $name;
    var $gender;
    
    public function say()
    {
        echo 'name is '.$this->name.' gender is '.$this->gender;
        echo "\r\n";
    }
}

$student = new person();
$student->name = 'tom';
$student->gender = 'male';
$student->say();

print_r((array)$student);
echo "\r\n";
var_dump( $student );

