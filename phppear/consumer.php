<?php
/**
 * User: Run
 * Date: 下午5:37 15-3-24
 * File: consumer.php
 * Desc: 
 */

require_once 'mongoLogCls.php';

abstract class amqpConsumer
{
    abstract public static function doTask($message);
}

class SolrConsumer extends amqpConsumer
{
    static $fp = null;
    static $logName = 'solr';

    public static function initialize(){
        self::logInit();
    }

    public static function doTask($msg){
        set_time_limit(0);

        //self::log2('test'.uniqid(), $msg);
        MongoLogCls::logSolr( $msg);
        try{
            $error = null;
            switch($msg['event']){
                case 'error':
                    break;
                case 'insert':

                    break;
            }
        }catch(Exception $e){

        }
    }

    public static  function logInit(){
        $dir =  __DIR__.DIRECTORY_SEPARATOR.'queuelog'.DIRECTORY_SEPARATOR;
        $log = $dir.self::$logName.'.log';

        if(!is_dir($dir)){
            mkdir($dir, 0777, 1);
        }


        $fileExists = file_exists($log);
        if(!$fileExists){
            touch($log);
        }
        chmod($log, 0777);

        if(!is_writable($dir)){
            die("目录不可写");
        }
        if( ($fileExists && !is_writable($log)) ){
            $info = sprintf('Unable to open the log file "%s" for writing', $log);
            die($info."\r\n");
        }

        self::$fp = fopen($log, 'a');

    }

    public static function log($message)
    {
        $line = sprintf("%s %s [%s]  %s%s", strftime("%b %d %H:%M:%S"), self::$logName, 'run', $message, DIRECTORY_SEPARATOR == '\\' ? "\r\n" : "\n");

        flock(self::$fp, LOCK_EX);
        fwrite(self::$fp, $line);
        flock(self::$fp, LOCK_UN);
    }

    public static  function log2($str, $vars = null)
    {
        if ($vars) {
            $str .= "\t" . json_encode($vars);
        }
        flock(self::$fp, LOCK_EX);
        fwrite(self::$fp, date("Y-m-d H:i:s") ."\t".$str."\n");
        flock(self::$fp, LOCK_UN);
    }
}


SolrConsumer::initialize('solr');