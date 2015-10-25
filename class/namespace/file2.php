<?php

namespace Foo\Bar;

include_once 'file1.php';

const FOO = 2;
function foo() {
  echo 'FOO2 func '.__NAMESPACE__.' fn '.__FUNCTION__, PHP_EOL;
}
class foo
{
  static function staticmethod()
  {
    echo 'FOO2 ns '.__NAMESPACE__.' cls '.__CLASS__.' method '.__METHOD__, PHP_EOL;
  }
}

echo '非限定名称 FILE2 Foo====', PHP_EOL;

foo();
foo::staticmethod();

echo '限定名称 FILE1 Foo====', PHP_EOL;

subnamespace\foo();
subnamespace\foo::staticmethod();

echo '完全限定名称 FILE2 Foo====', PHP_EOL;
\Foo\Bar\foo();
\Foo\Bar\foo::staticmethod();

echo \Foo\Bar\FOO, PHP_EOL;

echo \STRLEN('111'), PHP_EOL;

print_r(\INI_ALL);
