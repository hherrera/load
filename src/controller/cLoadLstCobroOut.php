<?php
require_once  '../../vendor/autoload.php';


/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if(!isset($argv[1])|| empty($argv[1])){
    die ("Falta año y mes");
}

$year= $argv[1];

$month= $argv[2];




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

$periodo = array('year'=>$year,'month'=>$month);

// OJO INACTIVA
$estado =3;

$c->deleteCartera($periodo,$estado );

// get contratos INactivos
$outactives = $c->getAllContractOUT();

// client de Jobs
$client = new GearmanClient();
// por defecto el localhost
$client->addServer();

$client->setCompleteCallback("complete"); 
$client->setStatusCallback("status");

$total =count($outactives);
$count=0;

// ciclo ejecucion;
foreach($outactives as $row){
    
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