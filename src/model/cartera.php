<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//require_once 'data.php';

class cartera {
     private $_conn;
     
     
    
    function __construct (data $conn)
    {
        
        $this->_conn=$conn;
        
       
    }
    
    
    public function getCobradoresEspecial(){
        
        return array('000','009');
    }
    
    public function getCobradoresActivos(){
        
                
        
        
        
        return array('003','006','008');
    }
    
    public function getCobradoresJuridico(){
        
        
        $lstCobJur = array('004');
        
        return $lstCobJur[0];
    }
    
    
    
    public function getLstCobro($id){
        
       $q="select * from lstcobro where id = '$id' ";
       $r=$this->_conn->_query($q);
        
       $row = mssql_fetch_array($r);
       return $row;
        
    }
    
    
    
    
    
    
    public function getOnlyPay($periodo,$hour){
        
         $q="select l.id from lstcobro l where l.ano=".$periodo['year']." and l.mes = ".$periodo['month']."
and l.id_contrato
 in( select distinct id_contrato 
from log_pagos 
where datediff(hour,created,getdate())<=".$hour." 
and TIPO_DOC IN(3,13,20) 
 AND  id_concepto IN (1, 2, 9, 99, 40))";
        $r=$this->_conn->_query($q);
   echo $q;     
        $list = array();
        
         while( $row = mssql_fetch_array($r)){
            
          $list[]=$row;   
             
         }
       
          
          
          
          return $list;
        
    }
    
    
    public function getAllContractON() {
        
        $q="select id_contrato from contratos c where c.estado = 2 order by id_contrato";
        $r=$this->_conn->_query($q);
        
        $list = array();
        
         while( $row = mssql_fetch_array($r)){
            
          $list[]=$row;   
             
         }
       
          
          
          
          return $list;
        
        
        
        
    }
    
    
    
    public function getAllLstCobro($periodo) {
        
        $q="select l.id from lstcobro l where l.ano=".$periodo['year']." and l.mes = ".$periodo['month'];
        $r=$this->_conn->_query($q);
        
        $list = array();
        
         while( $row = mssql_fetch_array($r)){
            
          $list[]=$row;   
             
         }
       
          
          
          
          return $list;
        
    }
    
    
   
    
    
    /*
     * Eliminar periodo completo
     * 
     */
    
    public function deleteCartera($periodo){
        
        $q="delete from lstcobro where ano=".$periodo['year']." and mes =".$periodo['month'];
        
        return $this->_conn->_query($q);
    }
    
    
    
    
    
    
    public function updateContrato($param){
        
        $id= $param['id']; // id del lstcobro
        $periodo=$param['periodo'];
        
          $lst= $this->getLstCobro($id);
        
        $c = new contrato($this->_conn, $lst['id_contrato']);
        
        $result= $c->getContrato();
        
        
        // calcular recaudo
        
        $recaudo = $c->getRecaudoContrato($periodo);
        
        $logro = $result['saldo'] <=0? 0:round($recaudo/$result['saldo'],2)*100;
        
        
        $q="update lstcobro set "
                . " ult_modificacion= getdate(),"
                . "saldo_a=".$result['saldo'].","
                . "diasmora_a=".$result['mora'].","
                . "recaudo=".$recaudo.","
                . "canon_a=".$result['canon'] .","
                . "iva_a=".$result['iva'].","
                . "logro=".$logro
                . " where id = ".$id;
                
        
         $r=$this->_conn->_query($q);
       
         if($r!=1){
             
             $result =0;
         }
         else{
             $result = $id;
         }
        return $result;
        
        
    }
    
    
    /*
     * Insertar contrato en periodo
     * array('id'=>$id,'periodo'=>array('year'=>$year,'month'=>$month))
     */
    
    public function insContrato($param)
    {
        
        $id= $param['id'];
        $periodo=$param['periodo'];
        
        $c = new contrato($this->_conn, $id);
        
        $result= $c->getContrato();
        
        
       
        $timezone = new DateTimeZone("Bogota");
        $date = new DateTime("now", $timezone);

        $label="";
        $meta=0;
        $factor=0;
        $tags="";
        
        $q= "insert into lstcobro (id_contrato,id_cliente,nombre,ano,mes,ult_modificacion,"
                . "diasmora_i,saldo_i,saldo_a,diasmora_a,canon_i,label,meta,factor,"
                . "estado,iva_i,canon_a,iva_a,tags) values (";
        
        $q.= $result['id_contrato'].",";
        $q.= "'".$result['id_cliente']."',";
        $q.= "'".$result['nombre']."',";     
        $q.= $periodo['year'].",";
        $q.= $periodo['month'].",";  
        $q.= "getdate(),";
        $q.= $result['mora'].",";
        $q.= $result['saldo'].",";
        $q.= $result['saldo'].",";
        $q.= $result['mora'].",";
        $q.= $result['canon'].",";
        $q.= "'".$label."',";
        $q.= $meta.",";
        $q.= $factor.",";
        $q.= $result['estado'].",";
        $q.= $result['iva'].",";
        $q.= $result['canon'].",";
        $q.= $result['iva'].",";
        $q.= "'".$tags."'";        
        $q.= ")";
                
        
      
         $r=$this->_conn->_query($q);
       
         if($r!=1){
             
             $result =0;
         }
         else{
             $result = $result['id_contrato'];
         }
        return $result;
        
       
        
    }
    
    
    
    /*
     * Asignar cobrador
     */
    public function setLabel($id){
        
       
        // traer cobradores por etapas 1: activos; 2: juridicos; 3: especiales
        
        
        // cargar conceptos para clasificar cartera
        
        $lst= $this->getLstCobro($id);
        
        // cargar contrato 
        $c = new contrato($this->_conn, $lst['id_contrato']);
        
        $co= $c->getContrato();
        
        
        // validar reglas de clasificacion
        // obtener etiqueta
        if($lst['diasmora_i']>=0 && $lst['diasmora_i']<=30 && $co['act_juridico']==0){
           $label ="0-30"; 
        }else if( $lst['diasmora_i']>30 && $lst['diasmora_i']<=60 && $co['act_juridico']==0){
           $label ="30-60"; 
        }else if( $lst['diasmora_i']>60 && $co['act_juridico']==0){
         $label ="Juridica"; 
        }else if(  $co['act_juridico']==1){
         $label ="Juridica"; 
        }
        else
        {
            $label ="No establecido";
            
        }
                        
            
        // actualizar lstcobro
        $q="update lstcobro set label ='".$label."' where id =$id";
         $r=$this->_conn->_query($q);
        
        // actualizar contrato
        
        
        
        return $label;
        
    }
    
    
     public function setCobrador($id){
        
               
        $lst= $this->getLstCobro($id);
        
        // cargar contrato 
        $c = new contrato($this->_conn, $lst['id_contrato']);
        
        $co= $c->getContrato();
        
    
        //  no establecer si es cartera especial
        
        $cob_especial= $this->getCobradoresEspecial();
        $id_cobrador_act=trim($co['cobrador']['id_cobrador']);
         if ( $id_cobrador_act=='000'){
             echo "este es 0 ->";
         }
        
        if(!in_array($id_cobrador_act,$cob_especial)){
        
        echo " 1. ";
        // validar reglas de clasificacion
        //  juridico
        if( ($lst['diasmora_i']>60 && $co['act_juridico']==0) || ($co['act_juridico']==1) ){
         
             echo " 2. ";
            $id_cobrador =trim($this->getCobradoresJuridico());
            
        }
                        
         if( ($lst['diasmora_i']<=60 && $co['act_juridico']==0)  ){
              echo " 3. ";
            $periodo = array('year'=>$lst['ano'],'month'=>$lst['mes']);
            $id_cobrador =trim( $this->getCobMin($this->getCobradoresActivos(),$periodo));
          
            
            
        }
        
        
        
       
        
        //actualizar contrato ??? falta guardar contrato anterior
              
        $q1="update contratos set id_cobrador ='".$id_cobrador."' "
                ." where id_contrato =".$co['id_contrato'];
        
         
        $r=$this->_conn->_query($q1);
        
        
         echo $co['id_contrato']." ???";  
        
        }else
        {
            
            $id_cobrador =$id_cobrador_act;
         
          
           echo "+++++++++++++++++".$id_cobrador_act."\n";
        }
        
        
         // actualizar lstcobro
        $q="update lstcobro set id_cobrador ='".$id_cobrador."' , id_user ='".$this->getUserCob($id_cobrador)
                ."' where id =$id";
        $r=$this->_conn->_query($q);
        
        
        return $id_cobrador;
        
    }
    
    
     public function getUserCob($id) {
        
        $q="select c.usuario as id_user from cobradores c where c.id_cobrador = '$id' ";
        $r=$this->_conn->_query($q);
        
          $row = mssql_fetch_array($r);
        return trim($row['id_user']);
        
    }
    
    
    
    
    
     public function getCobMin($lst =array(), $periodo ){
        
            
        $lstCobAct=array('003','006','008');
        
        
        $str_cob=implode("','", $lstCobAct);
        
        $q= "select sum(l.canon+l.admi)  as N , l.id_cobrador from contratos l"
                . " where l.id_cobrador in('".$str_cob."') "
                . " and l.estado=2"
                    . " group by l.id_cobrador order by sum(l.canon+l.admi) asc";
        
     
        
        
        $r=$this->_conn->_query($q);
        
        $row = mssql_fetch_array($r);
                
        return trim($row['id_cobrador']);
    }
    
    
    
    
    public function getConc(){
       
        $string = file_get_contents("/var/www/load/conf/conc.json");
       
    
        $json_a=json_decode($string,true);
        
       return  $json_a;
        
        
        
    }
    
    
    
    public function actLstCob($periodo){
        
        $q="select  id from lstCobro where ano=".$periodo['year']." and "
                . " mes = ".$periodo['month'] ." order by id asc ";
        
        
        
        $r=$this->_conn->_query($q);
        
        
        
         while( $row = mssql_fetch_array($r)){
            
            
             echo $this->setLabel($row['id']);
             
           echo  $this->setCobrador($row['id']);
         
            
         }
       
        
        
        
    }
    
    public function genMetasRangos($year,$month){
        
        
 $q="delete lstCobro_metas where year=".$year." and month=".$month;
  $this->_conn->_query($q);

// traer id_cobrador y label
               
        $q="select id_cobrador, label
            from lstcobro
            where ano=".$year.
            " and mes=".$month.
            " group by id_cobrador, label
            order by id_cobrador, label";

         $r=$this->_conn->_query($q);
                
         while( $row = mssql_fetch_array($r)){
         
             
             $sql ="insert into lstCobro_metas(year,month,id_cobrador,label,p_meta)";
             $sql.= " values (".$year.",".$month.",'".$row['id_cobrador']."',".
                     "'".$row['label']."',0)";
             
              $this->_conn->_query($sql);
             
            
         }
         
         return 0;
        
    }
    
    
}