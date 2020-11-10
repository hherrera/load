<?php

require_once  '../../../vendor/autoload.php';

$db = new data (array(
    'server' =>'10.102.1.3'
 ,'user' =>'sa'
 ,'pass' =>'i3kygbb2'
 ,'database' =>'sifinca' 
 ,'engine'=>'mssql'
   
));


$barrios = new barrios($db);


/* barrios cartagena*/
$res = $barrios->get('CTG');
 foreach ($res as $key => $value) {
 	
 	var_dump( $res[$key]);



 }


