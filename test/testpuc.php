<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once  '../vendor/autoload.php';

$db = new data (array(
 'server' =>'10.102.1.3'
 ,'user' =>'sa'
 ,'pass' =>'i3kygbb2'
 ,'database' =>'mca' 
 ,'engine'=>'mssql'
   
));

$b= new cuenta($db);

$datos = $b->getJson();

$filename = "puc-ctg.json";

file_put_contents($filename, $datos);

echo $datos;
