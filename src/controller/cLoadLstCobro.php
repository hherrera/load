<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once  '../../vendor/autoload.php';

/*
$sentryClient = new Raven_Client('https://88960df742bc490ea35594936f8eac2b:8650107386e54f3fa1799ee1d41bbe58@sentry.io/1194622');

$sentryClient->install();

 // bind the logged in user
$sentryClient->user_context(array('id'=>'hherrera', 'email' => 'hherrera@araujoysegovia.com'));

$sentryClient->tags_context(array(
    'php_version' => phpversion(),
    'ejecución'=> 'worker'
));
$sentryClient->setEnvironment('production');
 * 
 */

if(!isset($argv[1])|| empty($argv[1])){
    die ("Falta año y mes");
}

$year= $argv[1];
$month= $argv[2];

if(!isset($argv[3])|| empty($argv[3])){
    
    $ip = "10.102.1.3";
    $f = 'insContractLst';
    
}else{
    $ip = $argv[3];
    $f = 'insContractLst2';
}
if(!isset($argv[4])|| empty($argv[4])){
    $user = "sa";
}else{
   $user=$argv[4]; 
}

if(!isset($argv[5])|| empty($argv[5])){
    $pass = "i3kygbb2";
}else{
    $pass= $argv[5];
}

$periodo=array('year'=>$year,'month'=>$month);

$db = new data (array(
    'server' =>$ip
 ,'user' =>$user
 ,'pass' =>$pass
 ,'database' =>'sifinca' 
 ,'engine'=>'mssql'
   
));

$tt1 = microtime(true);

// crear objeto manejo cartera
$c = new cartera($db);


// eliminar lstcobro del periodo si esta ... recibir parametros 

$estado = 2;
$c->deleteCartera($periodo,$estado);

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

echo $f;

// ciclo ejecucion;
foreach($actives as $row){
    
    $param = array('id'=>$row['id_contrato'],'periodo'=>$periodo);
    $json = json_encode($param);

   
    // crear task
   //$job_handle = $client->addTask($f,$json,null,$row['id_contrato']);
   
   $c->insContrato(array('id'=>$row['id_contrato'],'periodo'=>$periodo));
    
     
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