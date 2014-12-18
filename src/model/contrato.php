<?php


class contrato {
    
    private $_conn;
    private $_id;
    
    
function __construct (data $conn, $id )
    {
        
        $this->_conn=$conn;
        
        $this->_id=$id;
    }

    public function getSaldo($periodo =null){
       
        
        
        $saldo=array();
        
     
        return $saldo;
        
    }
    
    
    /*
     * $periodo ->array('year'=>$year,'month'=>$month)
     * $cuenta ->string
     */
    public function getSaldoConta($cuenta,$periodo){
        
         
         
        $q="select nat from puc where id_cuenta='".$cuenta."'";
        $r=$this->_conn->_query($q);
        $nat = mssql_fetch_array($r);
        $ldb=$nat['nat'];


        //- SALDO ANTERIORES

        $npos=strlen($cuenta);   
        $cad="SAL_ANT ";

        for($i=1;$i<=$periodo['month'];$i++)
        {
            $mes= strlen($i)==1? '0'.$i:$i;
                if($ldb=='D'){
                 $cad=$cad." + MOVDB".$mes."-MOVCR".$mes;  
                }else{
                    $cad=$cad." + MOVCR".$mes."-MOVDB".$mes; 

                }		  
        } 
		

        $cad=$cad." as deuda,MOVDB".$mes."+SAL_ANT as db ";

         $month= strlen($periodo['month'])==1? '0'.$periodo['month']:$periodo['month'];

        $q="SELECT ".$cad;
        $q.=" FROM SALDOS ";
        $q.=" WHERE AÑO=".$periodo['year'];
        $q.=" AND ID_CUENTA='".$cuenta."'";
        
 
        
        $r=$this->_conn->_query($q);
         $row = mssql_fetch_array($r);



        return $row['deuda']==null ? 0 : $row['deuda'];
       
        
    }
    
     public function getSaldoFac(){
       
        
        $sql="select sum(f.tdr+f.tdo-f.tcr-f.tco+f.ivd-f.ivc) as saldo
		from factura_cxc f
			 where f.id_contrato=".$this->_id." AND f.tdr+f.tdo-f.tcr-f.tco+f.ivd-f.ivc >0";
			
	
        $r=$this->_conn->_query($sql);
        
        $row = mssql_fetch_array($r);
        
     
        return $row['saldo']==null? 0: $row['saldo'];
        
    }
    
    
    
    
    
    public function setId($id){
        
        $this->_id= $id;
        
    }
    
    /*
     * @parameter $id int
     * codigo del contrato
     */
    public function getOwnContract(){
        
        
        $q="select c.id_cliente, c.nombre +' '+ c.apellido as nombre, c.regimen_iva,c.retenedor,c.nat_juridica";
        $q.=" from clientes c , clientes_convenios cc, convenios co, inmuebles i, inmuebles_convenios ic, contratos_inmuebles ci ";
        $q.=" where ci.id_contrato=".$this->_id;
        $q.=" AND ci.id_inmueble=i.id_inmueble";
        $q.=" AND i.id_inmueble = ic.id_inmueble";
        $q.=" AND ic.id_convenio = co.id_convenio";
        $q.=" AND co.id_convenio = cc.id_convenio";
        $q.=" AND cc.id_cliente = c.id_cliente";
        
        $r=$this->_conn->_query($q);
        
        $co = mssql_fetch_array($r);
        
        return $co;
        
    }
    
   public function getParameter($idparametro){
       
       $q="select * from parametros where id_parametro=".$idparametro;
       
        $r=$this->_conn->_query($q);
        
        $row = mssql_fetch_array($r);
        
        return $row;
       
   }
  
    public function getReversionesContrato($periodo){
        
        
        
        $q="SELECT SUM(l.valor + l.iva) as r FROM LOG_PAGOS l"
           . " WHERE YEAR(l.fecha_pago) = ".$periodo['year']
           ." AND MONTH(l.fecha_pago) =".$periodo['month']
           ." AND l.TIPO_DOC IN(12) "
          ." AND l.id_concepto IN (1, 2, 9, 99, 40) "
           ." AND l.id_contrato = ".$this->_id;

          $r=$this->_conn->_query($q);
        
         $co = mssql_fetch_array($r);
        
        return is_null($co['r'])? 0: $co['r'];
        
    }
   
    public function getRecaudoContrato($periodo){
        
        
        
        $q="SELECT SUM(l.valor + l.iva)  as r FROM LOG_PAGOS l"
           . " WHERE YEAR(l.fecha_pago) = ".$periodo['year']  
           ." AND   MONTH(l.fecha_pago) =".$periodo['month']
           ." AND l.TIPO_DOC IN(3,13,20) "
           ." AND  l.id_concepto IN (1, 2, 9, 99, 40) "
           ." AND l.id_contrato = ".$this->_id;                

          $r=$this->_conn->_query($q);
        
         $co = mssql_fetch_array($r);     
        
        return is_null($co['r'])? 0: $co['r'];
        
    }
   
    
   
   public function getRecaudoContratoOut($periodo){
        
        
        
        $q="SELECT SUM(l.valor + l.iva)  as r FROM LOG_PAGOS l"
           . " WHERE YEAR(l.fecha_pago) = ".$periodo['year']  
           ." AND   MONTH(l.fecha_pago) =".$periodo['month']
           ." AND l.TIPO_DOC IN(3,13,20) "
           ." AND  l.id_concepto IN (1, 2, 9, 99, 40,, 3, 4, 20, 47, 31, 32, "
           ." 33, 51, 38, 21, 22, 23, 24, 34, 26, 25, 27, 28,58) "
           ." AND l.id_contrato = ".$this->_id;                

          $r=$this->_conn->_query($q);
        
         $co = mssql_fetch_array($r);     
        
        return is_null($co['r'])? 0: $co['r'];
        
    }
   
    
    
    public function getSalCartera($periodo){
        
        
        
        $q="SELECT dbo.getsalcartera("
           . $periodo['year']  
           .",".$periodo['month']
                     ." ,".$this->_id.") as r";                

          $r=$this->_conn->_query($q);
        
         $co = mssql_fetch_array($r);     
        
        return is_null($co['r'])? 0: $co['r'];
        
    }
    
   public function getCobrador($id){
       
         $q="select * from cobradores where id_cobrador='".$id."'";
       
        $r=$this->_conn->_query($q);
        
        $row = mssql_fetch_array($r);
        
        return $row;
       
   }
    
   /*
    * 
    *   
    */
    public function getContrato(){
        
              
        // traer data contrato y titular del contrato
        $q=" select  c.id_contrato , c.canon+ c.admi as canon, c.tipo_contrato, cl.nombre +' '+ cl.apellido as nombre";
        $q .= " ,fecha_retiro, datediff(day,fecha_retiro,getdate()) as dias_r, c.estado , c.id_sucursal, c.fianza, "
                . " c.env_fianza ,c.id_cobrador, c.act_juridico ,cl.id_cliente ";
        $q .= " from contratos c, clientes cl, clientes_contratos cc ";
        $q .=" where c.id_contrato=".$this->_id;
        $q .=" and  c.id_contrato=cc.id_contrato ";
        $q .=" and cc.tipo_relacion ='P'";
        $q .=" and cc.id_cliente= cl.id_cliente";
        
       
         
        $r=$this->_conn->_query($q);
        
      
        $co = mssql_fetch_array($r);
        
       
        $own = $this->getOwnContract();
       
        
        // calcular iva si aplica
        
       $p_iva = $this->getParameter(72)['valor'];
       
     
       if(($co['tipo_contrato']!='V') && (trim(strtoupper($own['regimen_iva']))=='COMUN')){
       
           $iva =round(($p_iva/100)*$co['canon'],0);
           
       }else{
            
        $iva=0;
       }
       
      
      
       //$saldo=$this->getSalCartera(array('year'=>2014,'month'=>6),$this->_id);
       $saldo=$this->getSaldoFac();
       
      
       if($co['estado']==2){
             $mora =round(($saldo/($co['canon']+$iva))*30);
       }else
       {
           $dias = is_null($co['dias_r'])?0:$co['dias_r'];
           
           $mora =$saldo>0?round(($saldo/($co['canon']+$iva))*30) + $dias:0;
           
       }
        
              
        
       return array('id_contrato'=>$co['id_contrato'],
           'canon'=>$co['canon'],
           'tipo_contrato'=>$co['tipo_contrato'],
           'nombre'=>$co['nombre'],
           'estado'=>$co['estado'],
           'id_sucursal'=>$co['id_sucursal'],
           'fianza'=>$co['fianza'],
           'env_fianza'=>$co['env_fianza'],
           'act_juridico'=>$co['act_juridico'],
           'propietario'=>$own,
           'cobrador'=>$this->getCobrador($co['id_cobrador']),
           'saldo'=>$saldo,
           'mora'=>$mora,
            'id_cliente'=>$co['id_cliente'],
           'iva'=>$iva
               );
       
        
    }
    
    
}