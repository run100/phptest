<?php

class SolrConsumer extends LsSimpleConsumer
{
  public static function doTask($message)
  {
    $command = $message['command'];

    switch($command) {
      case 'reload_cores':
        LsSolr::reloadAllCores();
        break;

      default:
        //unknown command: do nothing
    }
      
  }
}
