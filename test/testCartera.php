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


//echo $c->insContrato(array('id'=>11237,'periodo'=>array('year'=>2014,'month'=>6)));

//var_dump($c->getAllContractON());

$id = 20788;
//print_r($c->getOnlyPay(array('year'=>2014,'month'=>7),1));
print_r($c->genMetasRangos(2014,7));



//echo $c->setCobrador($id);

//echo $c->updateContrato(array('id'=>$id,'periodo'=>array('year'=>2014, 'month'=>7)));

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
    

