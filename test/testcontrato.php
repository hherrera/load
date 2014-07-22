<?php


require_once  '../vendor/autoload.php';

$db = new data(array(
    'server' =>'10.102.1.3'
 ,'user' =>'sa'
 ,'pass' =>'i3kygbb2'
 ,'database' =>'sifinca' 
 ,'engine'=>'mssql'
   
));

$c = new contrato($db,11237);


//print_r($c->getSaldoConta("13800500021",array('year'=>2014,'month'=>6)));

print_r($c->getRecaudoContrato(array('year'=>2014,'month'=>7)));

echo "\n";

//print_r($c->getContrato());




