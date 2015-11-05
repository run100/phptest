<?php

class emailException extends Exception
{

}

class pwdException extends Exception
{
  public function __toString()
  {
    //$arr = array($this->getCode(),$this->getMessage(),$this->getFile(), $this->getLine());
    return $this->getMessage();
  }
}

try {
  $pwd = '';
  if ( empty($pwd) ) {
    throw new pwdException("密码为空");
  }
} catch(pwdException $e) {
  echo $e;
  echo PHP_EOL;
}

exit;
$a = null;

try {
  $a = 5/0;
  echo $a, PHP_EOL;
} catch (exception $e) {
  echo $e->getMessage();
  $a = -1;
}

echo $a, PHP_EOL;
