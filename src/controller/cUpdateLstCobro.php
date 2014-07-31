#!/usr/bin/php
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once  '../../vendor/autoload.php';


$db = new data (array(
    'server' =>'10.102.1.3'
 ,'user' =>'sa'
 ,'pass' =>'i3kygbb2'
 ,'database' =>'sifinca' 
 ,'engine'=>'mssql'
   
));

$tt1 = microtime(true);

// crear objeto manejo cartera
$c = new cartera($db);

$year= date("Y");
$month=date("m");
$month=8;

       
        
// eliminar lstcobro del periodo si esta ... recibir parametros 

$periodo = array('year'=>$year,'month'=>$month);


$hour= $argv[1];

if($hour){
    $actives = $c->getOnlyPay($periodo,$hour);
}else {
    $actives = $c->getAllLstCobro($periodo);
}
// get TODA la lista
//

// solo los han pagado



// client de Jobs
$client = new GearmanClient();
// por defecto el localhost
$client->addServer();

$client->setCompleteCallback("complete"); 
$client->setStatusCallback("status");

$total =count($actives);
$count=0;

// ciclo ejecucion;
foreach($actives as $row){
    
    $param = array('id'=>$row['id'],'periodo'=>$periodo);
    $json = json_encode($param);

   
    // crear task
   $job_handle = $client->addTask("actContractLst",$json,null,$row['id']);
       
    $count++;
echo "Sending job ".$row['id']." -> $count - ".  round(100*($count/$total),2)."% \n ";
    
    
}






if(!$client->runTasks())
{
    echo "ERROR " . $client->error() . "\n";
    exit;
}


$tt2 = microtime(true);
$r= $tt2-$tt1;
echo "\n\nTiempo de ". $r." para $count registros\n";


function complete($task) 
{ 
  print "COMPLETE: " . $task->unique()  . "\n"; 
}


function status($task)
{
    echo "STATUS: " . $task->unique() . ", " . $task->jobHandle() . " - " . $task->taskNumerator() . 
         "/" . $task->taskDenominator() . "\n";
}





?>