<?php

require_once  '../../vendor/autoload.php';

$sentryClient = new Raven_Client('https://88960df742bc490ea35594936f8eac2b:8650107386e54f3fa1799ee1d41bbe58@sentry.io/1194622');

$sentryClient->install();

 // bind the logged in user
$sentryClient->user_context(array('id'=>'hherrera', 'email' => 'hherrera@araujoysegovia.com'));

$sentryClient->tags_context(array(
    'php_version' => phpversion(),
    'ejecución'=> 'worker'
));
$sentryClient->setEnvironment('production');

## Obtener el token de session = SessionID

$url="http://www.sifinca.net/sifinca/web/app.php/login";
$headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
   
);

$a = new api($url, $headers);

 $result= $a->post(array("user"=>"sifinca@araujoysegovia.com","password"=>"araujo123"));  

 $data=json_decode($result);
  
 if(is_object($data)){
        $token = $data->id;
 }else
 {
    $token="";
     
 }
## cargar objetos para inyectar a workers
$url="http://www.sifinca.net/sifinca/web/app.php/archive/main/history/accounting";
$headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
    'x-sifinca:SessionToken SessionID="'.$token.'", Username="sifinca@araujoysegovia.com"'  ,  
);


$a->set(array('url'=>$url,'headers'=>$headers));


## TODO: login -> obtener token y modificar cabecera
$w = new GearmanWorker();
$count=0;
$w->addServer();
$w->addFunction("insDocument","insDocument",$a);
//$w->addFunction("actContractLst","actContractLst",$c);

while($w->work()){

if ($w->returnCode() != GEARMAN_SUCCESS)
  {
    echo "return_code: " . $w->returnCode() . "\n";
    break;
  }

} 
  
### aqui las funciones 
  
function insDocument($job,&$data){
    
    $param=json_decode( $job->workload());
    
    ## Post en accounting mongodb
     $result= $data->post( $param);  
    
      return "\n ".$result;
    
    
      
    
}