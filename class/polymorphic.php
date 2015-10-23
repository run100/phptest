<?php

class employee
{
  protected function working()
  {
    echo '本方法需要重载才能执行', PHP_EOL;
  }


}

class teacher extends employee
{
  public function working()
  {
    echo '教书', PHP_EOL;
  }
}

class coder extends employee
{
  public function working()
  {
    echo 'coding', PHP_EOL;
  }
}

function do_print($obj)
{
  if ( get_class($obj) == 'employee' ) {
    echo 'error,基类';
  } else {
    $obj->woring();
    call_user_method('working', $obj);
  }
}


do_print(new teacher);
do_print(new coder);
do_print(new employee);
