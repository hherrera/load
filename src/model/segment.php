<?php


class segment {
    
    private $_conn;
    private $_maxc;
    private $_maxi;
    private $_maxd;
    
function __construct (data $conn)
    {
        
        $this->_conn=$conn;
        
      $this->_maxc=0.9;
      
      $this->_maxi=0.05;
      $this->_maxd=0.05;
      
    }
    
    /*
     * todos los propietarios agrupados por cedula
     */
  public function getAllClientIds(){
      
        $q=" select distinct cl.id_cliente, cl.nombre + ' '+ cl.apellido as name, estado
        from convenios c, clientes_convenios cc, clientes cl
         where c.id_convenio = cc.id_convenio
         and cc.id_cliente = cl.id_cliente 
         and cc.tipo_relacion ='P'";
        
         $r = $this->_conn->_query($q);
         
                

        return $this->_conn->_getData($r);
      
      
  }
    

  public function getPropertiesActive($id){
      $sql = "select count(co.id_contrato) as n, sum((ic.por_seguro + ic.por_cmsi)*(co.canon + co.admi)/100)  as value
        from clientes_convenios cc, convenios c, inmuebles_convenios ic, inmuebles i
        , contratos_inmuebles ci, contratos co, clientes cl
        where cl.id_cliente='$id'
        and cl.id_cliente = cc.id_cliente
        and cc.tipo_relacion = 'P'
        and cc.id_convenio = c.id_convenio
        and cc.id_convenio = ic.id_convenio
        and ic.id_inmueble = i.id_inmueble
        and i.id_inmueble = ci.id_inmueble
        and ci.id_contrato = co.id_contrato
        and co.estado =2";
      
      
       $r = $this->_conn->_query($sql);
         
        return $this->_conn->_getData($r);
      
  }
  
  
  public function getdaysLast($id){
     
      // falta implementar esta función
      
      $sql = "";
      
      
       //$r = $this->_conn->_query($sql);
         
        return 1;
      
  }
  
  
 
  public function getPropertiesEnabled($id){
      $sql = "select count(i.id_inmueble) as n
        from clientes_convenios cc, convenios c, inmuebles_convenios ic, inmuebles i
        , clientes cl
        where cl.id_cliente='$id'
        and cl.id_cliente = cc.id_cliente
        and cc.tipo_relacion = 'P'
        and cc.id_convenio = c.id_convenio
        and cc.id_convenio = ic.id_convenio
        and ic.id_inmueble = i.id_inmueble
        and i.promocion = 1
        ";
      
      
       $r = $this->_conn->_query($sql);
         
        return $this->_conn->_getData($r);
      
  }
  
  
  
  public function getConvenios($id){
      $sql = "select c.id_convenio
        from clientes_convenios cc, convenios c,  clientes cl
        where cl.id_cliente='$id'
        and cl.id_cliente = cc.id_cliente
        and cc.tipo_relacion = 'P'
        and cc.id_convenio = c.id_convenio
       ";

      
       $r = $this->_conn->_query($sql);
         
        return $this->_conn->_getData($r);
      
  }
  
  
  
  public function insert($data){
      
      $sql = "insert segments ( [id_cliente]
      ,[name]
      ,[year]
      ,[month]
      ,[comision]
      ,[antiguedad]
      ,[inmuebles]
      ,[status]
      ) values
      ("
      ."'".$data['id_cliente']."'"
      .",'".$data['name']."'"
      .",".$data['year']
      .",".$data['month']
      .",".$data['comision']  
      .",".$data['antiguedad'] 
      .",".$data['inmuebles'] 
      .",'".$data['status'] ."'"
      
.")";
     
      echo $sql;
      
       return $this->_conn->_query($sql);
      
  }
  
  public function Update($data){
      
      
      $sql =" update segments set "
      ." ,[p_comision]=".$data['p_comision']
      .",[p_antiguedad]=".$data['p_antiguedad']
      .",[p_inmuebles]=".$data['p_inmuebles']
      .",[s_comision]=".$data['s_comision']
      .",[s_antiguedad]=".$data['s_antiuedad']
      .",[s_inmuebles]=".$data['p_comision']
      .",score = " .$data['score']
      ." where id =".$data['id']        
              ;
       return $this->_conn->_query($sql);
      
  }
  
  
  public function delete($periodo){
      
       $sql ="delete segments where year=".$periodo['year']." and month=".$periodo['month'];
      
       return $this->_conn->_query($sql);
  }
  
 
   public function getMaxComi($periodo){
      
      $sql ="select max(comision)  as max from segments where year=".$periodo['year']." and month=".$periodo['month'];
      
       
      $r = $this->_conn->_query($sql);
         
       $data= $this->_conn->_getData($r);
      
      
      return $data[0]['max'];
       
       
   
      
      
  }
  
  
   public function getMaxProperty($periodo){
      
      
      $sql ="select max(inmuebles)  as max from segments where year=".$periodo['year']." and month=".$periodo['month'];
      
       
      $r = $this->_conn->_query($sql);
         
       $data= $this->_conn->_getData($r);
      
      
      return $data[0]['max'];
       
  }
   
  
  public function getMaxDays($periodo){
      
      
      $sql ="select max(antiguedad)  as max from segments where year=".$periodo['year']." and month=".$periodo['month'];
      
       
      $r = $this->_conn->_query($sql);
         
       $data= $this->_conn->_getData($r);
      
      
      return $data[0]['max'];
       
  }
 
  
  public function updatePScore($periodo){
      
      $sql = " update segments set"
            
              . " p_comision = ".$this->_maxc 
              . " ,p_inmuebles = ".$this->_maxi 
              . " ,p_antiguedad = ".$this->_maxd
              . " where year=".$periodo['year']."  and month=".$periodo['month'];
      
     
      
    $this->_conn->_query($sql);
      
     return 0; 
      
  }
  
  
  public function updateValNull($periodo){
      
      $sql = " update segments set"          
              . " s_inmuebles = 0 "
              . " where  s_inmuebles is null and year= ".$periodo['year']."  and month=".$periodo['month'];
      
     echo $sql;
      
     $this->_conn->_query($sql);
    
    
      $sql = " update segments set"          
              . " s_antiguedad = 0 "
              . " where  s_antiguedad is null and year = ".$periodo['year']."  and month=".$periodo['month'];
      
     
      
    $this->_conn->_query($sql);
      
     return 0; 
      
  }
  
  public function updateScore($periodo)
  {
      
      $maxc = $this->getMaxComi($periodo); 
      $maxi = $this->getMaxProperty($periodo);
      $maxd = $this->getMaxDays($periodo);
      $this->updatePScore($periodo);
      
     $this->updateValNull($periodo);
      
   
     echo "reglas comision\n";
      $this->updateScoreComi($periodo, 4, 5, 1000000, $maxc);
      $this->updateScoreComi($periodo, 3, 4, 500000, 1000000);
      $this->updateScoreComi($periodo, 0, 3,5000, 500000);
      
     echo "reglas inmuebles\n";
      $this->updateScoreInmu($periodo, 4, 5,1, 5);
      $this->updateScoreInmu($periodo, 3, 4,5, 10);
      $this->updateScoreInmu($periodo, 1, 3,10, $maxi);
      
      
      
      /* 
       * $this->updateScoreDias($periodo, 4, 5,0, 60);
      $this->updateScoreDias($periodo, 3, 4,60, 120);
      $this->updateScoreDias($periodo, 1, 3,120, $maxd);
       * 
       * . ", s_inmuebles = ( 1+ (1/".$maxi.") -(inmuebles/".$maxi.") )*5"
              . ", s_antiguedad = ( 1+ (1/".$maxd.") -(antiguedad/".$maxd.") )*5"
       * 
       *  actualizar score
       * 
       *  $sql = " update segments set"
              . " score = p_antiguedad*s_antiguedad + p_inmuebles*s_inmuebles + p_comision*s_comision"   
              . " where year=".$periodo['year']."  and month=".$periodo['month'];
       */
      
        $sql = " update segments set"
              . " score = p_inmuebles*s_inmuebles + p_comision*s_comision"   
              . " where year=".$periodo['year']."  and month=".$periodo['month'];
         
      
       $this->_conn->_query($sql);
    
     
      echo "intervalos\n";
       
       $this->setIntervals($periodo, 0, 0.5, 'Blanco');
        $this->setIntervals($periodo, 0.5, 1, 'Gris');
       $this->setIntervals($periodo, 1, 3, 'Verde');
       $this->setIntervals($periodo,3, 4, 'Plata');
       $this->setIntervals($periodo,4, 5, 'Dorado');
      //
      //
      
      // regla 1  4-5  comision > 1000000
   return 0;
      
      
  }        
          
public function  setIntervals($periodo,$s1,$s2,$segment){
    
    
     $sql = " update segments set"
              . " segment ='".$segment."'"   
              . " where year=".$periodo['year']."  and month=".$periodo['month']
              ." and score> ".$s1." and score <=".$s2;
         
      
       $this->_conn->_query($sql);
    
    return 0;
}
  
  
  
  public function updateScoreComi($periodo,$c1,$c2,$i1,$i2){
      
      
      $sql = " update segments set"
              . " s_comision =".$c1." +( comision/".$i2 ." )*(".$c2."- ".$c1.") "   
              . " where year=".$periodo['year']."  and month=".$periodo['month']
              ." and (comision >".$i1." and comision <=".$i2." )";
   
      
    $this->_conn->_query($sql);
      
     return 0; 
  }
  
  
  public function updateScoreInmu($periodo,$c1,$c2,$i1,$i2){
      
      
      $sql = " update segments set"
              . " s_inmuebles =".$c2." - ( inmuebles/".$i2 ." )*(".$c2."- ".$c1.") "   
              . " where year=".$periodo['year']."  and month=".$periodo['month']
              ." and (inmuebles >".$i1." and inmuebles <=".$i2." )";
 
      
    $this->_conn->_query($sql);
      
     return 0; 
  }
  
  
  public function updateScoreDias($periodo,$c1,$c2,$i1,$i2){
      
      
      $sql = " update segments set"
              . " s_antiguedad =".$c2." - ( antiguedad/".$i2 ." )*(".$c2."- ".$c1.") "   
              . " where year=".$periodo['year']."  and month=".$periodo['month']
              ." and (antiguedad >".$i1." and antiguedad <=".$i2." )";

      
    $this->_conn->_query($sql);
      
     return 0; 
  }
  
  
  public function getAllSegments($periodo){
      
       $sql = "select *
        from segments s
        where s.year=".$periodo['year']
        ." and s.month=".$periodo['month'];
       

      
       $r = $this->_conn->_query($sql);
         
        return $this->_conn->_getData($r);
      
      
  }
  
  
  public function updateChild($periodo){
      
      // traer registros del periodo
      
      
      $s = $this->getAllSegments($periodo);
      
      // recorrer array
      
      foreach ($s as $v1){
          
        echo $v1['id_cliente'] ."\n";
          // traer convenios
           $co = $this->getConvenios($v1['id_cliente']);
          
          
            //recorrer los convenios
          foreach($co as $v2){
              
              // actualizar
              echo $v2['id_convenio']."\n";
              echo $v1['segment']."\n";
              
              $sql = " update convenios set "
              . " segment ='".$v1['segment']."'"   
              . " where id_convenio=".$v2['id_convenio'];
   
             
            $this->_conn->_query($sql);
              
              
          }
          
      }
      
      
      
      
  }
  
  
  
}


