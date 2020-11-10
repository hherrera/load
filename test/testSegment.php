#!/usr/bin/php
<?php


require_once  '../vendor/autoload.php';

$year= 2017;
$month= 12;

$db = new data(array(
    'server' =>'10.102.1.3'
 ,'user' =>'sa'
 ,'pass' =>'i3kygbb2'
 ,'database' =>'sifinca' 
 ,'engine'=>'mssql'
   
));

$s = new segment($db);


$periodo = array("year"=>$year, "month"=> $month );


echo "Borrando periodo";
$s->delete($periodo);

echo "Cargando todos propietarios";
$allclients = $s->getAllClientIds();


foreach ($allclients as $key => $prop) {
    
    
    $properties1 = $s->getPropertiesActive($prop['id_cliente']); 
    

    
    $properties2 = $s->getPropertiesEnabled($prop['id_cliente']);  
  
    $p1 = $properties1[0]['n'];
    $p2 = $properties2[0]['n'];
    $inmuebles = $p1 + $p2;
    $dias_a = $s->getDaysLast($prop['id_cliente']);
   
    $dias = $dias_a[0]['dias_ult_captacion'];
    $dias = ( empty($dias)? 0: $dias );
      
    $inmuebles =(empty($inmuebles)? 0:$inmuebles);
    
    $comision = (empty($properties1[0]['value'])?0: $properties1[0]['value']);
    
    $status= ($prop['estado']=='V'?'Activo':'Inactivo');
   
   $data = array(
       "id_cliente"=> $prop['id_cliente'],
       "name"=> $prop['name'],
     "year"=> $year,
     "month"=> $month,
     "comision"=> $comision, 
      "antiguedad"=> $dias,
      "inmuebles"=> $inmuebles,
      "status"=> $status
       
   );
   
     print_r($data);
    $s->insert($data);
   
    
}








return 0;


