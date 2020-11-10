<?php

require_once  '../../vendor/autoload.php';
/*
$sentryClient = new Raven_Client('https://88960df742bc490ea35594936f8eac2b:8650107386e54f3fa1799ee1d41bbe58@sentry.io/1194622');
$sentryClient->install();
*/


$t1 = microtime(true);

/*
// bind the logged in user
$sentryClient->user_context(array('id'=>'hherrera', 'email' => 'hherrera@araujoysegovia.com'));

$sentryClient->tags_context(array(
    'php_version' => phpversion(),
    'ejecución'=> 'worker'
));

$sentryClient->setEnvironment('production');
*/

$db = new data (array(
    'server' =>'10.102.1.3'
 ,'user' =>'sa'
 ,'pass' =>'i3kygbb2'
 ,'database' =>'sifinca' 
 ,'engine'=>'mssql'
   
));

$db2 = new data (array(
    'server' =>'192.168.100.5'
 ,'user' =>'sa'
  ,'pass' =>'75080508360'
 ,'database' =>'sifinca' 
 ,'engine'=>'mssql'
   
));

// crear objeto manejo cartera
$c = new cartera($db);

$c2 = new cartera($db2);


$t2 = microtime(true);
$r= $t2-$t1;


$w = new GearmanWorker();

$count=0;
$w->addServer();
$w->addFunction("insContractLst","insContractLst",$c);
$w->addFunction("actContractLst","actContractLst",$c);

$w->addFunction("insContractLst2","insContractLst2",$c2);
$w->addFunction("actContractLst2","actContractLst2",$c2);
while($w->work()){

if ($w->returnCode() != GEARMAN_SUCCESS)
  {
    echo "return_code: " . $w->returnCode() . "\n";
    break;
  }



}



function insContractLst($job,&$data){
    
    $param=json_decode( $job->workload());
    
    
    $client= $data->insContrato(array('id'=>$param->id,'periodo'=>array('year'=>$param->periodo->year,'month'=>$param->periodo->month)));
    
    
    return "\n Complete id:".$client;
    
}


function actContractLst2($job,&$data){
    
    $param=json_decode( $job->workload());
    
    
    $client= $data->updateContrato(array('id'=>$param->id,'periodo'=>array('year'=>$param->periodo->year,'month'=>$param->periodo->month)));
    
    
    return "\n Complete id:".$client;
    
}

function insContractLst2($job,&$data){
    
    $param=json_decode( $job->workload());
    
    
    $client= $data->insContrato(array('id'=>$param->id,'periodo'=>array('year'=>$param->periodo->year,'month'=>$param->periodo->month)));
    
    
    return "\n Complete id:".$client;
    
}


function actContractLst($job,&$data){
    
    $param=json_decode( $job->workload());
    
    
    $client= $data->updateContrato(array('id'=>$param->id,'periodo'=>array('year'=>$param->periodo->year,'month'=>$param->periodo->month)));
    
    
    return "\n Complete id:".$client;
    
}
