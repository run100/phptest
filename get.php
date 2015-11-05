<?php 

error_reporting(E_ALL);

#file_get_contents('http://192.168.148.132:8983/solr/collection1/deltaimport');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://192.168.148.132:8983/solr/collection1/deltaimport');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$data = curl_exec($ch);
curl_close($ch);
