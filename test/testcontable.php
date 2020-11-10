<?php


require_once  '../vendor/autoload.php';



$db = new data (array(
 'server' =>'10.102.1.3'
 ,'user' =>'sa'
 ,'pass' =>'i3kygbb2'
 ,'database' =>'mca' 
 ,'engine'=>'mssql'
   
));

$b= new contable($db);

$p = array('NUMEDCTO'=>'0000273509','FNTEDCTO'=>'70' );

//$p = array('ANODCTO'=>'201401','FNTEDCTO'=>'01');

//$p = array('YEAR'=>'2015');

$datos = $b->getDocument($p);

print_r($datos);

//echo $datos[0]['NUMEDCTO'];



return;



$url="http://104.130.27.244/sifinca/web/app.php/archive/main/history/accounting";
$headers = array(
    'Accept: application/json',
    'Content-Type: application/json',
    'x-sifinca:SessionToken SessionID="55043c4ecd7959470102b7be", Username="sifinca@araujoysegovia.com"'  ,  
);

$accounting = new api($url, $headers);




foreach ($datos as $key => $value) {
    
  

   
     $map= array("voucher"=> $value['NUMEDCTO'],
  
   "source"=> $value['DESFUENTE'],
   "sourceCode"=> $value['FNTEDCTO'],
"description"=> $value['DESCDCTO'],
   "thirdIdentity"=> $value['IDTERCERO'],
   "third"=> $value['tercero'] ,
         "date"=>$value['FECHDCTO']);
 
     print_r($map );
     
     // enviar a ruta
     
  $result= $accounting->post($map);  
     
  print_r($result);
} 



