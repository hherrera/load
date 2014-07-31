#!/usr/bin/php
<?php


require_once  '../vendor/autoload.php';


$db = new data(array(
    'server' =>'10.102.1.3'
 ,'user' =>'sa'
 ,'pass' =>'i3kygbb2'
 ,'database' =>'sifinca' 
 ,'engine'=>'mssql'
   
));

$c = new contrato($db, 12196);


var_dump($c->getContrato());

$year= date("Y");
$month=date("n");

echo $year ." ".$month;

//print_r($c->getSaldoConta("13800500021",array('year'=>2014,'month'=>6)));

//print_r($c->getRecaudoContrato(array('year'=>2014,'month'=>7)));

echo "\n";

//print_r($c->getReversionesContrato(array('year'=>2014,'month'=>7)));


echo "\n";

//print_r($c->getSalCartera(array('year'=>2014,'month'=>6)));

//print_r($c->getContrato());




