<?php

namespace Foo\Bar\subnamespace;

const FOO = 1;
function foo(){
  echo 'FOO1 func '.__NAMESPACE__.' fn '.__FUNCTION__, PHP_EOL;
}

class foo
{
  public function __construt()
  {
    echo 'FILE1 '.__CLASS__, PHP_EOL;
  }


  static function staticmethod()
  {
    echo 'FOO1 ns '.__NAMESPACE__.' cls '.__CLASS__.' method '.__METHOD__, PHP_EOL;
  }
}