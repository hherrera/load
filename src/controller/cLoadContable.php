<?php

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
 * */


if(!isset($argv[1])|| empty($argv[1])){
    die ("Falta año ");
}

$year= $argv[1];



$db = new data (array(
    'server' =>'10.102.1.3'
 ,'user' =>'sa'
 ,'pass' =>'i3kygbb2'
 ,'database' =>'mca' 
 ,'engine'=>'mssql'
   
));
$tt1 = microtime(true);
echo "conectando a la base de datos \n";

$b= new contable($db);
$month= $argv[2];
$periodo=array('year'=>$year,'month'=>$month);

if(!isset($argv[2])|| empty($argv[2])){
   $p = array('YEAR'=>$periodo['year']);
}else
{
    $anodcto= $periodo['year'].$periodo['month'];
   $p = array('ANODCTO'=>$anodcto); 
    
}
//$p = array('NUMEDCTO'=>'0000273512','FNTEDCTO'=>'70' );

//$p = array('ANODCTO'=>$anodcto);

//$p = array('YEAR'=>$periodo['year']);

echo "Cargando documentos ... \n";
$datos = $b->getDocument($p);

// client de Jobs
$client = new GearmanClient();
//por defecto el localhost
$client->addServer();

$client->setCompleteCallback("complete"); 
$client->setStatusCallback("status");

$total =count($datos);
$count=0;

// ciclo ejecucion;
foreach($datos as $key => $value){
    $v = $value['FNTEDCTO'].'-'.$value['NUMEDCTO'];
    
    $map = array("voucher"=> $value['NUMEDCTO'] ,
                "voucherSource"=> $v ,
                "source"=> $value['DESFUENTE'],
                "sourceCode"=> $value['FNTEDCTO'],
                "description"=> $value['DESCDCTO'],
                "thirdIdentity"=> $value['IDTERCERO'],
                "third"=> $value['tercero'] ,
                "date"=>$value['FECHDCTO']
     );
    $count++; 
                    
   
    
    //$data = '{"voucher":"0000273512","voucherSource":"70-0000273512","source":"FACTURA                       ","sourceCode":"70","description":"Gar. Inmo. de 201401 \/ SALCEDO TORO GIOVANNA MARCE","thirdIdentity":"890400048","third":"ARAUJO Y SEGOVIA S.A.                   ","date":"2014\/01\/31"}';
    
  // $res=$api->post($map);
   
    //$result= _callapi('POST', $url, $headers, $data);  
    
   
    $json = json_encode($map);
// crear task
   $job_handle = $client->addTask("insDocument",$json,null,$v);
       
  
//echo "Sending job ".$value['NUMEDCTO']." -> $count - ".  round(100*($count/$total),2)."% \n ";
    
    
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