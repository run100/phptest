<?php

$dir = new DirectoryIterator( dirname(__FILE__) );

//print_r($dir);
foreach( $dir as $fileinfo ) {
  if ( !$fileinfo->isDir() ) {
    print_r($fileinfo);
    echo PHP_EOL;
  }
}
