#!/usr/bin/php
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
 
 
+/
 * 
 */

$ip = "10.102.1.3";
 $f = "actContractLst";
 $user = "sa";
 $pass = "i3kygbb2";
 $hour = 99;
 
 
 echo $argv[1];
 
if($argv[1]){
    $ip = $argv[1];
    
	if($ip=='192.168.100.1'){
		$f = "actContractLst2";
	}
	else{
		$f = "actContractLst";
		
	}
}
if($argv[2]){
   
   $user=$argv[2]; 
}

if($argv[3]){

    $pass= $argv[3];
}


// contextualizar errores

/*
$sentryClient->breadcrumbs->record(array(
    'message' => 'Authenticating DB user as ' . $user,
    'category' => 'auth',
    'level' => 'info',
));
*/


$db = new data (array(
    'server' =>$ip
 ,'user' =>$user
 ,'pass' =>$pass
 ,'database' =>'sifinca' 
 ,'engine'=>'mssql'
   
));


if (!$db) {
    // ...
    $sentryClient->captureLastError();
}



$tt1 = microtime(true);

// crear objeto manejo cartera
$c = new cartera($db);

if (isset($argv[5])) {
        if($argv[5]){
           $year= $argv[5]; 
}}

    else{
      $year= date("Y");  
    }

if (isset($argv[6])) {  
            if($argv[6]){
                   $month= $argv[6]; 
} }
    else{
    $month=date("m"); 
    }



// eliminar lstcobro del periodo si esta ... recibir parametros 

$periodo = array('year'=>$year,'month'=>$month);

print_r($periodo);

if (isset($argv[4])) { 
    if($argv[4]){
         $hour= $argv[4];
    }
}

if($hour<24){
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

echo $f;
// ciclo ejecucion;
foreach($actives as $row){
    
    $param = array('id'=>$row['id'],'periodo'=>$periodo);
    $json = json_encode($param);

   
   if($hour<24){
       echo $c->updateContrato(array('id'=>$row['id'],'periodo'=>$periodo));
      //  $job_handle = $client->addTask($f,$json,null,$row['id']);
   }else{ 
       // crear task
        echo $c->updateContrato(array('id'=>$row['id'],'periodo'=>$periodo));
       
	   //$job_handle = $client->addTask($f,$json,null,$row['id']);
       
  } 
   
   
   
       
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
