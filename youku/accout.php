<?php
/**
 * User: Run
 * Date: 14:19 2015/10/10
 * File: accout.php
 * Desc:
 */

/*
18356961571
xiaoling
*/
$accout = '';

$post = array(
  'client_id' => '8480aead46280621',
  'client_secret' => '1cab07c4e4db3e3b9b3e7b2e57d89d3a',
  'grant_type' => 'password',
  'username' => '18356961571',
  'password' => 'xiaoling',
);

$ch = curl_init("https://openapi.youku.com/v2/oauth2/authorize");
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
$html = curl_exec($ch);

print_r($html);
