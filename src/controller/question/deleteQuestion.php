<?php
require_once  '../../../vendor/autoload.php';



$url="http://soylider.sifinca.net/lider/web/app.php/admin/home/question/";
$headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
    'x-login: Username Username="dmejia@araujoysegovia.com", Password="araujo123"'  ,  
);

$q = new api($url, $headers);


 $q->delete();
