<?php

require_once  '../../../vendor/autoload.php';


$url="http://soylider.sifinca.net/home/player/";
$headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
    'x-login: Username Username="dmejia@araujoysegovia.com", Password="araujo123"'  ,  
);

$player = new api($url, $headers);


/*

$db = new data (array(
    'server' =>'10.102.1.3'
 ,'user' =>'sa'
 ,'pass' =>'i3kygbb2'
 ,'database' =>'nomina_ays' 
 ,'engine'=>'mssql'
   
));

*/

$db = new data (array(
    'server' =>'192.168.100.5'
 ,'user' =>'sa'
 ,'pass' =>'75080508360'
 ,'database' =>'nomina' 
 ,'engine'=>'mssql'
   
));


$player1 = new empleados($db);


$players=$player1->get();





$i=0;
foreach( $players as $p ){
    $i++;
    echo $i." : ". $p['email']." \n";
    
    $p=array('name'=>$p['nombre'],
        'email'=>strtolower($p['email']),
        'lastname'=>$p['apellido'],
        'image'=>'',
        'office'=>array('id'=>1),
        'roles'=>array('id'=>2),
        'team'=>array()
        );
    
    
        echo $player->post($p);
    
    
}


/*



}-
id: 1
email: "dmejia@araujoysegovia.com"
name: "Deiner"
lastname: "Mejia"
image: null
office: null
roles: [1]
0:  {
deleted: false
entrydate: {
date: "2014-08-04 13:08:17"
timezone_type: 3
timezone: "America/Bogota"
}-
lastupdate: {
date: "2014-08-04 13:08:17"
timezone_type: 3
timezone: "America/Bogota"
}-
id: 1
name: "ADMIN"
description: "Administrator role"
}-
-
team: null
}
 * 
 * */
