<?php

require_once  '../vendor/autoload.php';

$client = new Raven_Client('https://88960df742bc490ea35594936f8eac2b:8650107386e54f3fa1799ee1d41bbe58@sentry.io/1194622');
$client->install();


$client->tags_context(array(
    'php_version' => phpversion(),
));

$client->setEnvironment('production');

// bind the logged in user
$client->user_context(array('email' => 'admin@sifinca.net'));


$db = new data (array(
 'server' =>'10.102.1.3'
 ,'user' =>'sa'
 ,'pass' =>'i3kygbb2'
 ,'database' =>'sifinca_bog' 
 ,'engine'=>'mssql'
   
));


$ciudad = $argv[1];





$b= new barrios($db);
$datos = $b->getJson($ciudad);
echo $datos ;
$filename = "barrios-".$ciudad.".json";
file_put_contents($filename, $datos);



$z = new zonas($db);
$zonas = $z->getJson($ciudad);
$filename = "zonas-".$ciudad.".json";
file_put_contents($filename, $zonas);
echo "\n".$filename;
echo "\n Json generados...";



/*

$db2 = new data (array(
    'server' =>'127.0.0.1'
 ,'user' =>'root'
 ,'pass' =>'araujo123'
 ,'database' =>'test' 
 ,'engine'=>'mysql'
   
));

$player1 = new empleados($db);


$players=$player1->get();

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
}
*/
