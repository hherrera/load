<?php

require_once  '../../../vendor/autoload.php';
use Guzzle\Http\Client;
use GuzzleHttp\Exception\RequestException;        

$db = new data (array(
    'server' =>'10.102.1.3'
 ,'user' =>'sa'
 ,'pass' =>'i3kygbb2'
 ,'database' =>'sifinca' 
 ,'engine'=>'mssql'
   
));

$db2 = new data (array(
    'server' =>'192.168.100.1'
 ,'user' =>'sa'
 ,'pass' =>'75080508360'
 ,'database' =>'sifinca' 
 ,'engine'=>'mssql'
   
));


$p = new preguntas($db);

$result1= $p->getAllPreguntas();

$p2 = new preguntas($db2);

$result2= $p2->getAllPreguntas();

$result= array_merge($result1,$result2);


 

$url="http://soylider.sifinca.net/lider/web/app.php/admin/home/question/";
$headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
    'x-login: Username Username="dmejia@araujoysegovia.com", Password="araujo123"'  ,  
);

$question = new api($url, $headers);




foreach ($result as $pregunta ){
   
    $answer=array();
    
     $answer[]=array('answer'=> $pregunta['resp_1'],'selected'=> $pregunta['resp_ok']==1?true:false);
     $answer[]=array('answer'=> $pregunta['resp_2'],'selected'=> $pregunta['resp_ok']==2?true:false);
     $answer[]=array('answer'=> $pregunta['resp_3'],'selected'=> $pregunta['resp_ok']==3?true:false);
     $answer[]=array('answer'=> $pregunta['resp_4'],'selected'=> $pregunta['resp_ok']==4?true:false);
      
    $data=array('question'=>$pregunta['descripcion'], 'hasImage'=>false,'category'=>array('id'=> 1),'answers'=>$answer);
    
    echo "\n";
    echo json_encode($data);
    
    $response= $question->post($data);
    
   echo "\n";
  echo $response;
  
    
}



 
  