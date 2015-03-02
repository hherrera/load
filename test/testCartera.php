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




$id = 20788;

$ids=array(
    952	,
1281	,
7857	,
10847	,
11455	,
12189	,
13111	,
13578	,
13618	,
13680	,
13820	,
14128	,
14189	,
14562	,
14606	,
14683	,
14710	,
14853	,
14943	,
14944	,
15003	,
15005	,
15138	,
15253	,
15295	,
15358	,
15521	,
15523	,
15565	,
16010	,
16051	,
16071	,
16292	,
16336	,
16583	,
16814	,
17330	,
17344	

);

//
//foreach($ids as $id){
 //  echo  $c->setCobradorIdContrato($id,array('year'=>2014,'month'=>8)); 
//}

//echo $c->insContrato(array('id'=>11237,'periodo'=>array('year'=>2014,'month'=>6)));

//var_dump($c->getAllContractOUT());

//print_r($c->getOnlyPay(array('year'=>2014,'month'=>7),1));
//print_r($c->genMetasRangos(2014,7));


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
    

