<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once  './../vendor/autoload.php';





## Obtener el token de session = SessionID

$url="http://www.sifinca.net/sifinca/web/app.php/login";
$headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
   
);

$a = new api($url, $headers);

 $result= $a->post(array("user"=>"sifinca@araujoysegovia.com","password"=>"araujo123"));  

 
 $data=json_decode($result);
  
$token = $data->id;

echo $token;
    