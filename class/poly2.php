<?php

interface employee
{
  public function working();
}

class teacher implements employee
{
  public function working()
  {
    echo '教书育人', PHP_EOL;
  }
}

class coder implements employee
{
  public function working()
  {
    echo '码农', PHP_EOL;
  }
}

function do_print(employee $i)
{
  //echo $i->working();
  call_user_method('working', $i);
}

do_print(new teacher);
do_print(new coder);


