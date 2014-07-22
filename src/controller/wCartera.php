<?php

require_once  '../../vendor/autoload.php';
$t1 = microtime(true);


$db = new data (array(
    'server' =>'10.102.1.3'
 ,'user' =>'sa'
 ,'pass' =>'i3kygbb2'
 ,'database' =>'sifinca' 
 ,'engine'=>'mssql'
   
));

// crear objeto manejo cartera
$c = new cartera($db);




$t2 = microtime(true);
$r= $t2-$t1;



$w = new GearmanWorker();

$count=0;
$w->addServer();
$w->addFunction("insContractLst","insContractLst",$c);
$w->addFunction("actContractLst","actContractLst",$c);
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


function actContractLst($job,&$data){
    
    $param=json_decode( $job->workload());
    
    
    $client= $data->updateContrato(array('id'=>$param->id,'periodo'=>array('year'=>$param->periodo->year,'month'=>$param->periodo->month)));
    
    
    return "\n Complete id:".$client;
    
}