<?php

require_once  '../../vendor/autoload.php';

$db = new data (array(
    'server' =>'10.102.1.3'
 ,'user' =>'sa'
 ,'pass' =>'i3kygbb2'
 ,'database' =>'sifinca' 
 ,'engine'=>'mssql'
   
));

$c = new cartera($db);

//echo $c->getNumContractON();

echo "\n";

if(!isset($argv[1])|| empty($argv[1])){
    die ("Falta año y mes");
}

$year= $argv[1];
$month= $argv[2];

$periodo=array('year'=>$year,'month'=>$month);

$c->actLstCob($periodo);
        
     
echo "\n Proceso terminado.";


?>
    

