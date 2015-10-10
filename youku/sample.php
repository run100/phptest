<?php

/*****YoukuUpload SDK*****/
header('Content-type: text/html; charset=utf-8');
include("include/YoukuUploader.class.php");

$client_id = "8480aead46280621"; // Youku OpenAPI client_id
$client_secret = "1cab07c4e4db3e3b9b3e7b2e57d89d3a"; //Youku OpenAPI client_secret

/*
**The way with username and password applys to the partner level clients!
**Others may use access_token to upload the video file.
**In addition, refresh_token is to refresh expired access_token. 
**If it is null, the access_token would not be refreshed.
**You may refresh it by yourself.
**Like "http://open.youku.com/docs/api/uploads/client/english" for reference.
*/

$params['access_token'] = ""; 
$params['refresh_token'] = "";
$params['username'] = ""; //Youku username or email
$params['password'] = md5(""); //Youku password

set_time_limit(0);
ini_set('memory_limit', '128M');
$youkuUploader = new YoukuUploader($client_id, $client_secret);
$file_name = ""; //video file
try {
    $file_md5 = @md5_file($file_name);
    if (!$file_md5) {
        throw new Exception("Could not open the file!\n");
    }
}catch (Exception $e) {
    echo "(File: ".$e->getFile().", line ".$e->getLine()."): ".$e->getMessage();
    return;
}
$file_size = filesize($file_name);
$uploadInfo = array(
		"title" => "", //video title
		"tags" => "", //tags, split by space
		"file_name" => $file_name, //video file name
		"file_md5" => $file_md5, //video file's md5sum
		"file_size" => $file_size //video file size
);
$progress = true; //if true,show the uploading progress 
$youkuUploader->upload($progress, $params,$uploadInfo); 
?>
