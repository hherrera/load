<?php



require_once  '../vendor/autoload.php';


$db = new data (array(
    'server' =>'10.102.1.3'
 ,'user' =>'sa'
 ,'pass' =>'i3kygbb2'
 ,'database' =>'sifinca' 
 ,'engine'=>'mssql'
));

$c = new cartera($db);
//echo $c->getNumContractON();
echo "\n";

$id = 112509;
$type="";

//$c = new contrato($db, $id);
//$co = $c->getContrato();
//var_dump($co);
       
$c = new cartera($db);

//echo $c->dif30("2016-10-05", "2016-10-15");¡
//var_dump( $c->getCarteraContrato($id));
//var_dump($c->getCarteraContrato($id));
//return 0;
//
//foreach($ids as $id){
 //  echo  $c->setCobradorIdContrato($id,array('year'=>2014,'month'=>8)); 
//}
//echo $c->insContrato(array('id'=>9150,'periodo'=>array('year'=>2017,'month'=>1)));
//var_dump($c->getAllContractOUT());
//print_r($c->getOnlyPay(array('year'=>2014,'month'=>7),1));
//print_r($c->genMetasRangos(2014,7));



$periodo = array('year'=>date("Y"), 'month'=>date("m"));


 $actives = $c->getAllLstCobro($periodo);
 $total =count($actives);
 $count=0;
 foreach($actives as $row){
    
    $param = array('id'=>$row['id'],'periodo'=>$periodo);
    $json = json_encode($param);

   
    // crear task
   //$job_handle = $client->addTask("actContractLst",$json,null,$row['id']);
    
    echo $c->updateContrato(array('id'=>$row['id'],'periodo'=>$periodo));
       
    $count++;
echo "Sending job ".$row['id']." -> $count - ".  round(100*($count/$total),2)."% \n ";
    
    
}
 
 


/*

 $cob_especial= $c->getCobradoresEspecial();
        $id_cobrador_act='000';
         
        if(!in_array($id_cobrador_act,$cob_especial)){
        
            echo "uno";
        }else
        {
            echo "dos";
        }





$json=$c->getConc();
$conc = $json['conc'];


 $diasmora=31;
          $act_juridico=0;

 foreach ($conc as $key => $value) {
          
            
            
      $label =$conc[$key]['name'];
      $type = $conc[$key]['Type'];
      $fields = $conc[$key]['fields'];
      
      $flag=0;
      
      echo "\n";
      foreach ($fields as $k ){
             
          
         
         // $field=$k['field'];
          // $type = $k['Type'];
          $expression='$result=('.$k['expression'].')? 1 : 0;' ;
          
                
          eval($expression);        
        
         if($result==0){
             
             $flag=1;
             
         }
        
          
      }
                        
        if($flag==1){
            
            echo $label;
            break;
        }    
     
     
   }

*/

?>
    

