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


// eliminar lstcobro del periodo si esta ... recibir parametros 

$periodo = array('year'=>'2014','month'=>'7');

$c->deleteCartera($periodo);

// get contratos activos
$actives = $c->getAllContractON();

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
    
    $param = array('id'=>$row['id_contrato'],'periodo'=>$periodo);
    $json = json_encode($param);

   
    // crear task
   $job_handle = $client->addTask("insContractLst",$json,null,$row['id_contrato']);
       
    $count++;
echo "Sending job ".$row['id_contrato']." -> $count - ".  round(100*($count/$total),2)."% \n ";
    
    
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