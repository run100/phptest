<?php


$redis = new Redis();
$redis->connect('127.0.0.1', '6379');




$key = 'queue';
echo $argv[1]."\r\n";
if($argv[1] == 'in'){
    $i = 0;
    while(True){
        try{
            $val = 'value_'.$i;
            $redis->lpush('queue', $val);
            sleep(rand()%3);

        }catch(Exception $e){
            print_r($e->getMessage())."\r\n";
        }
        $i++;
    }

}

if($argv[1] == 'out'){
    while(True){
        try{
            echo $redis->lpop('queue')."\r\n";
        }catch(Exception $e){
            echo $e->getMessage()."\r\n";
        }
        sleep(rand()%3);

    }

}