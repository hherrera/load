#!/usr/bin/php
<?php


require_once  '../vendor/autoload.php';

$year= 2017;
$month= 12;

$db = new data(array(
    'server' =>'10.102.1.3'
 ,'user' =>'sa'
 ,'pass' =>'i3kygbb2'
 ,'database' =>'sifinca' 
 ,'engine'=>'mssql'
   
));

$s = new segment($db);


$periodo = array("year"=>$year, "month"=> $month );





$s->updateScore($periodo);

$s->updateChild($periodo);
    









return 0;


