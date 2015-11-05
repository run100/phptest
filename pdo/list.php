<?php

try{
	$dbh = new PDO('mysql:host=localhost;dbname=jobeet', 'root', '');
	var_dump($dbh);
}catch(PDOException $e){
	print_r($e->getMessage()."\n");
	die();
}
#$dbh = new PDO('mysql:host=127.0.0.1;dbname=jobeet', 'root', '');
#var_dump($dbh);
