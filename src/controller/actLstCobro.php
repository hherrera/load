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
 * 
 */

//echo $c->getNumContractON();

echo "\n";

if(!isset($argv[1])|| empty($argv[1])){
    die ("Falta año y mes");
}

$year= $argv[1];
$month= $argv[2];


if(!isset($argv[3])|| empty($argv[3])){
    
    $ip = "10.102.1.3";
    $f =  "actContractLst";;
    
}else{
    $ip = $argv[3];
    $f = "actContractLst2";
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






$db = new data (array(
    'server' =>$ip
 ,'user' =>$user
 ,'pass' =>$pass
 ,'database' =>'sifinca' 
 ,'engine'=>'mssql'
   
));

$c = new cartera($db);


$periodo=array('year'=>$year,'month'=>$month);

echo $ip;
if($ip=='10.102.1.3'){
    echo "actlstcob Cartagena \n";
    $c->actLstCob($periodo,false);
}else{
      echo "actlstcob Monteria \n";
     $c->actLstCob($periodo,true);
}
        
     
echo "\n Proceso terminado.";


?>
    

