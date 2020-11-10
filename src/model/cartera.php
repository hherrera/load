<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class cartera {

    private $_conn;
    private $_parameters;

    function __construct(data $conn) {

        $this->_conn = $conn;
        
        $this->getparameters();
    }

    public function getCobradoresEspecial($flag) {
        
        
        if($flag){
            $result = array('005','121');
        }else{
            $result = array('000', '009');
        }
        return $result;
    }

    public function getCobradoresActivos($flag) {


     if($flag){
            $result = array('124', '120','008');
        }else{
          $result = array('003', '006');
        }
        return $result;


       
    }

    public function getCobradoresJuridico($flag) {

        
        if($flag){
            $result = array('991');
        }else{
           $result = array('004','008');
        }
        return $result[0];
       
    }

    public function getLstCobro($id) {

        $q = "select * from lstcobro where id = '$id' ";
        $r = $this->_conn->_query($q);

        $row = mssql_fetch_array($r);
        return $row;
    }

    public function getOnlyPay($periodo, $hour) {

        $q = "select l.id from lstcobro l where l.ano=" . $periodo['year'] . " and l.mes = " . $periodo['month'] . "
                and l.id_contrato
                 in( select distinct id_contrato 
                from log_pagos 
                where datediff(hour,created,getdate())<=" . $hour . " 
                and TIPO_DOC IN(3,13,20) ) ";
        $r = $this->_conn->_query($q);
      
        $list = array();

        while ($row = mssql_fetch_array($r)) {

            $list[] = $row;
        }

        return $list;
    }

    public function getAllContractON() {

        $q = "select id_contrato from contratos c where c.estado = 2 order by id_contrato";
        $r = $this->_conn->_query($q);

        $list = array();

        while ($row = mssql_fetch_array($r)) {

            $list[] = $row;
        }


        return $list;
    }

    public function getAllContractOUT() {

        // traer saldo mayor 0
        $q = "select id_contrato from contratos c where c.estado IN( 3,4)  ";

        $r = $this->_conn->_query($q);
        echo "\n 1";
        $list = array();

        while ($row = mssql_fetch_array($r)) {

            $list[] = $row;
        }

        return $list;
    }

    public function getAllLstCobro($periodo) {

        $q = "select l.id from lstcobro l where  l.ano=" . $periodo['year'] . " and l.mes = " . $periodo['month'];
        $r = $this->_conn->_query($q);

        $list = array();

        while ($row = mssql_fetch_array($r)) {

            $list[] = $row;
        }

        return $list;
    }

    /*
     * Eliminar periodo completo
     * 
     */

    public function deleteCartera($periodo, $estado) {

        $q = "delete from lstcobro where ano=" . $periodo['year'] . " and mes =" . $periodo['month'] . " AND estado =" . $estado;

        return $this->_conn->_query($q);
    }

    public function updateContrato($param) {

        $id = $param['id']; // id del lstcobro
        $periodo = $param['periodo'];

   
        
        $lst = $this->getLstCobro($id);
        
  

        $c = new contrato($this->_conn, $lst['id_contrato']);


        
        $result = $c->getContrato();

  
        // calcular recaudo
        if($lst['estado']==2){
               $recaudo = $c->getRecaudoContrato($periodo);
               $recaudoOff = $c->getRecaudoContratoOff($periodo);
        }else
            {
            $recaudo = $c->getRecaudoContratoOut($periodo);
             $recaudoOff = 0;
        }
     
        
        
        
        $reversiones = $c->getReversionesContrato($periodo);

        $logro = $result['saldo'] <= 0 ? 0 : round($recaudo / $result['saldo'], 2) * 100;


        $q = "update lstcobro set "
                . " ult_modificacion= getdate(),"
                . "saldo_a=" . $result['saldo'] . ","
                . "diasmora_a=" . $result['mora'] . ","
                . "recaudo=" . $recaudo . ","
                . "recaudooff=" . $recaudoOff . ","
                . "canon_a=" . $result['canon'] . ","
                . "iva_a=" . $result['iva'] . ","
                . "reversiones=" . $reversiones . ","
                . "logro=" . $logro
                . " where id = " . $id;


        $r = $this->_conn->_query($q);

        if ($r != 1) {

            $result = 0;
        } else {
            $result = $id;
        }
        return $result;
    }

    /*
     * Insertar contrato en periodo
     * array('id'=>$id,'periodo'=>array('year'=>$year,'month'=>$month))
     */

    public function insContrato($param) {

        $id = $param['id'];
        $periodo = $param['periodo'];

        $c = new contrato($this->_conn, $id);

        $result = $c->getContrato();

        $estado = $result['estado'] == "4" ? "3" : $result['estado'];

        if (($estado == "3" ) && $result['saldo'] < 0) {
            return $result['id_contrato'];
        }

//        $timezone = new DateTimeZone("Bogota");
 //       $date = new DateTime("now", $timezone);

        $label = "";
        $meta = 0;
        $factor = 0;
        $tags = "";

        $q = "insert into lstcobro (id_contrato,id_cliente,nombre,ano,mes,ult_modificacion,"
                . "diasmora_i,saldo_i,saldo_a,diasmora_a,canon_i,label,meta,factor,"
                . "estado,iva_i,canon_a,iva_a,tags) values (";

        $q.= $result['id_contrato'] . ",";
        $q.= "'" . $result['id_cliente'] . "',";
        $q.= "'" . $result['nombre'] . "',";
        $q.= $periodo['year'] . ",";
        $q.= $periodo['month'] . ",";
        $q.= "getdate(),";
        $q.= $result['mora'] . ",";
        $q.= $result['saldo'] . ",";
        $q.= $result['saldo'] . ",";
        $q.= $result['mora'] . ",";
        $q.= $result['canon'] . ",";
        $q.= "'" . $label . "',";
        $q.= $meta . ",";
        $q.= $factor . ",";
        $q.= $estado . ",";
        $q.= $result['iva'] . ",";
        $q.= $result['canon'] . ",";
        $q.= $result['iva'] . ",";
        $q.= "'" . $tags . "'";
        $q.= ")";



        $r = $this->_conn->_query($q);

        if ($r != 1) {

            $result = 0;
        } else {
            $result = $result['id_contrato'];
        }


        return $result;
    }

    /*
     * Asignar cobrador
     */

    public function setLabel($id) {


        // traer cobradores por etapas 1: activos; 2: juridicos; 3: especiales
        // cargar conceptos para clasificar cartera

        $lst = $this->getLstCobro($id);

        // cargar contrato 
        $c = new contrato($this->_conn, $lst['id_contrato']);

        $co = $c->getContrato();


       // $mora =  $lst['diasmora_i'];

       $mora = $co['mora'];

        // validar reglas de clasificacion
        // obtener etiqueta
        if ($co['estado'] == 3 || $co['estado'] == 4  ) {
            $label = "Desocupado";
        }
         else if ($mora>= 0 && $mora<= 31 && $co['act_juridico'] == 0 && $co['estado'] == 2) {
            $label = "0-30";
        } else if ($mora> 31 && $mora<= 61 && $co['act_juridico'] == 0 && $co['estado'] == 2) {
            $label = "30-60";
        } else if ($mora> 61 && $mora<= 91 && $co['act_juridico'] == 0 && $co['estado'] == 2) {
            $label = "60-90";
        } else if ($mora> 91 && $co['act_juridico'] == 0 && $co['estado'] == 2) {
            $label = "mas90";
        } else if ($co['act_juridico'] == 1 && $co['estado'] == 2) {
            $label = "Juridica";
        }  else {
            $label = "No establecido";
        }


        // actualizar lstcobro
        $q = "update lstcobro set label ='" . $label . "' where id =$id";
        $r = $this->_conn->_query($q);

        // actualizar contrato

        return $label;
    }

    public function getCobradoresInactivo($flag) {
        if($flag){
            $result ='130';
        }else{
            $result = '991';
        }
        return $result;
    }

    /*
 
     * $id lstcobro
     *    
     *  */
    
    
    public function setCobradorIdContrato($id_contrato,$periodo){
        
        
        $q= "select id from lstcobro where ano="
                . "".$periodo['year'].""
                . " and mes = ".$periodo['month']
                . " and id_contrato =".$id_contrato;
        
      
        $r = $this->_conn->_query($q);
        $co = mssql_fetch_array($r);
       
        return $this->setCobrador($co['id']);
        
        
        
        
    }
    
    
    public function setCobrador($id) {


        $lst = $this->getLstCobro($id);

        // cargar contrato 
        $c = new contrato($this->_conn, $lst['id_contrato']);

        $co = $c->getContrato();


        //  no establecer si es cartera especial

        $cob_especial = $this->getCobradoresEspecial(false);

        $id_cobrador_act = trim($co['cobrador']['id_cobrador']);
        if ($id_cobrador_act == '000') {
            echo "\n este es 0 ->";
        }

        //$diasmora=$lst['diasmora_i'];
        $diasmora=$co['mora'];
        
        if (!in_array($id_cobrador_act, $cob_especial)) {


            if (($lst['estado'] != 2)) {

                /// cobrador inactivo
                echo " 4. inactivo ";
                $id_cobrador = trim($this->getCobradoresInactivo(false));
            } else {

                echo "\n  1. ";
                // validar reglas de clasificacion
                //  juridico
                if (($diasmora > 60 && $co['act_juridico'] == 0) || ($co['act_juridico'] == 1)) {

                    echo "\n  2. juridico";
                    $id_cobrador = trim($this->getCobradoresJuridico(false));
                }

                if (($diasmora <= 60 && $co['act_juridico'] == 0)) {
                    echo "\n  3. 0-60";
                    $periodo = array('year' => $lst['ano'], 'month' => $lst['mes']);

                    // dejar el mismo si cobrador actual es activo

                    $cob_activos =$this->getCobradoresActivos(false);

                    if(in_array($id_cobrador_act, $cob_activos)){
                        $id_cobrador =$id_cobrador_act; 
                    }else
                    {

                        $id_cobrador = trim($this->getCobMin($cob_activos, $periodo));
                    }
                }
            }



            //actualizar contrato ??? falta guardar contrato anterior

            $q1 = "update contratos set id_cobrador ='" . $id_cobrador . "' "
                    . " where id_contrato =" . $co['id_contrato'];


            $r = $this->_conn->_query($q1);


            echo "\n ".$co['id_contrato'] . " *****";
        } else {

            $id_cobrador = $id_cobrador_act;


            echo "\n +++++++++++++++++" . $id_cobrador_act . "\n";
        }


        // actualizar lstcobro
        $q = "update lstcobro set id_cobrador ='" . $id_cobrador . "' , id_user ='" . $this->getUserCob($id_cobrador)
                . "' where id =$id";
        $r = $this->_conn->_query($q);


        return $id_cobrador;
    }

    
     public function setCobrador2($id) {


        $lst = $this->getLstCobro($id);

        // cargar contrato 
        $c = new contrato($this->_conn, $lst['id_contrato']);

        $co = $c->getContrato();

        //  no establecer si es cartera especial

        $cob_especial = $this->getCobradoresEspecial(true);

        $id_cobrador_act = trim($co['cobrador']['id_cobrador']);
        
        if ($id_cobrador_act == '000') {
            echo "\n este es 0 ->";
        }

        //$diasmora=$lst['diasmora_i'];
        $diasmora=$co['mora'];
        
        if (!in_array($id_cobrador_act, $cob_especial)) {


            if (($lst['estado'] != 2)) {

                /// cobrador inactivo
                echo " 4. inactivo ";
                $id_cobrador = trim($this->getCobradoresInactivo(true));
            } else {

                echo "\n  1. ";
                // validar reglas de clasificacion
                //  juridico
                if (  $co['act_juridico'] == 1  or   $diasmora > 90 ) {

                    echo "\n  2. juridico";
                    $id_cobrador = trim($this->getCobradoresJuridico(true));
                }

                if (( $co['act_juridico'] == 0) &&  ($diasmora <= 90)) {
                    echo "\n  3. 0-60";
                    $periodo = array('year' => $lst['ano'], 'month' => $lst['mes']);

                    // dejar el mismo si cobrador actual es activo

                    $cob_activos =$this->getCobradoresActivos(true);

                  
                    $id_cobrador = trim($this->getCobMin($cob_activos, $periodo));
               
                    
                    echo "\n".$id_cobrador;
                    // cambiar  si ???
                    
                     // es de cerete? id_ciudad = 'MON'
                    
                       if($co['id_ciudad']=='CER'  ){
                           
                            $id_cobrador = '004';
                           
                       }
                    
                       if($co['id_ciudad']=='MON' && $co['id_sector']==3 && $co['tipo_contrato']=='V' && $diasmora <= 30 ){
                           
                            $id_cobrador = '002';
                           
                       }
                       
                       if($co['deshaucio']==true  ){
                           
                            $id_cobrador = '005';
                           
                       }
                       
                     if($co['tipo_contrato']=='P'  ){
                           
                            $id_cobrador = '121';
                           
                       }
                       
                                       
                    
                }
            }



            
        } else {

            $id_cobrador = $id_cobrador_act;


            echo "\n +++++++++++++++++" . $id_cobrador_act . "\n";
        }


        //actualizar contrato ??? falta guardar contrato anterior

        $q1 = "update contratos set id_cobrador ='" . $id_cobrador . "' "
        . " where id_contrato =" . $co['id_contrato'];


        $r = $this->_conn->_query($q1);


        echo "\n ".$co['id_contrato'] . " *****";

        // actualizar lstcobro
        $q = "update lstcobro set id_cobrador ='" . $id_cobrador . "' , id_user ='" . $this->getUserCob($id_cobrador)
                . "' where id =$id";
        $r = $this->_conn->_query($q);


        return $id_cobrador;
    }

    
    
    
    
    
    public function getUserCob($id) {

        $q = "select c.usuario as id_user from cobradores c where c.id_cobrador = '$id' ";
        $r = $this->_conn->_query($q);

        $row = mssql_fetch_array($r);
        return trim($row['id_user']);
    }

    public function getCobMin($lst = array(), $periodo) {


     $lstCobAct = $lst;


     $str_cob = implode("','", $lstCobAct);
   
        $q = "select sum(l.canon+l.admi)  as N , l.id_cobrador from contratos l"
                . " where l.id_cobrador in('" . $str_cob . "') "
                . " and l.estado=2"
                . " group by l.id_cobrador order by sum(l.canon+l.admi) asc";


echo $q;

        $r = $this->_conn->_query($q);

        $row = mssql_fetch_array($r);

        return trim($row['id_cobrador']);
    }

    public function getConc() {

        $string = file_get_contents("/var/www/load/conf/conc.json");


        $json_a = json_decode($string, true);

        return $json_a;
    }

    public function actLstCob($periodo,$flag) {

        $q = "select  id from lstCobro where ano=" . $periodo['year'] . " and "
                . " mes = " . $periodo['month'] . " and estado = 2 order by id asc ";



        $r = $this->_conn->_query($q);



        while ($row = mssql_fetch_array($r)) {


            echo $this->setLabel($row['id']);
            echo "\n ";    
            
            
            if($flag){
                 echo "setCobrador2 Monteria\n";
            echo $this->setCobrador2($row['id']);
           
            
            }else{
                  echo "actlstcob Cartagena \n";
                 echo $this->setCobrador($row['id']);
               
                
            }
            echo "\n ";
        }
    }

    public function genMetasRangos($year, $month) {


        $q = "delete lstCobro_metas where year=" . $year . " and month=" . $month;
        $this->_conn->_query($q);

// traer id_cobrador y label

        $q = "select id_cobrador, label
            from lstcobro
            where ano=" . $year .
                " and mes=" . $month .
                " group by id_cobrador, label
            order by id_cobrador, label";

        $r = $this->_conn->_query($q);

        while ($row = mssql_fetch_array($r)) {


            $sql = "insert into lstCobro_metas(year,month,id_cobrador,label,p_meta)";
            $sql.= " values (" . $year . "," . $month . ",'" . $row['id_cobrador'] . "'," .
                    "'" . $row['label'] . "',0)";

            $this->_conn->_query($sql);
        }

        return 0;
    }

    
    
    // obtener parametros
    
    public function getparameters(){
        
       
        $q= "Select id_parametro,valor from parametros order by id_parametro asc";
        
          $r = $this->_conn->_query($q);
        
        $list= array() ;
        
        while($row = mssql_fetch_array($r)){
            
          $list[$row['id_parametro']]=$row['valor'];
            
         
        }
        
        
       $this->_parameters =  $list;
    
        
    }
    
    public function getparameter($id){
        
        
        return $this->_parameters[$id];
    
        
    }
    
    
    
    //  data del contrato de arriendo
    
    public function getContrato($id){
        
        $q= "execute trae_contrato ".$id.",2";
        
          $r = $this->_conn->_query($q);
        $row = mssql_fetch_array($r);
        return $row;
   
        
    }
    
     //  data del contrato de arriendo
    
    public function getConcPendientes($id,$type){
        //*--- conceptos arrendamiento --- solo arriendo --- por cada uno incluir
        $q1= " select detalle_cxc.tipo_doc, detalle_cxc.id_factura,detalle_cxc.tdr-detalle_cxc.tcr AS salr,detalle_cxc.tdo -detalle_cxc.tco as salo ,
            detalle_cxc.IVD -detalle_cxc.IVC as iva ,detalle_cxc.tipo_doc,  
	detalle_cxc.id_factura , detalle_cxc.id_concepto ,tipo_doc.nombre as tipo_docn ,
	 conceptos.descripcion as id_concepton,detalle_cxc.descripcion ,factura_cxc.fecha, 
         factura_cxc.periodo , factura_cxc.fecha_vencimiento,factura_cxc.fecha_mora ,
         detalle_cxc.tdr as totalr , detalle_cxc.tdo as totalo, detalle_cxc.tcr as pagor, detalle_cxc.tco as pagoo ,
         detalle_cxc.rfc, detalle_cxc.rfd, detalle_cxc.rid , detalle_cxc.ric, detalle_cxc.ivd, detalle_cxc.ivc
	from detalle_cxc, factura_cxc, conceptos, tipo_doc
	  where factura_cxc.id_contrato=  $id 
	  and factura_cxc.tipo_doc = detalle_cxc.tipo_doc    
	  and factura_cxc.id_factura=detalle_cxc.id_factura    
	  and detalle_cxc.tipo_doc = tipo_doc.tipo_doc  
	  and detalle_cxc.id_concepto = conceptos.id_concepto     
	  and detalle_cxc.tdr+detalle_cxc.tdo -(detalle_cxc.tcr+detalle_cxc.tco)>0    
	  order by factura_cxc.periodo desc  ";
        
         $q2= " select detalle_cxc.tdr-detalle_cxc.tcr AS salr,detalle_cxc.tdo -detalle_cxc.tco as salo ,
            detalle_cxc.IVD -detalle_cxc.IVC as iva ,detalle_cxc.tipo_doc,  
	detalle_cxc.id_factura , detalle_cxc.id_concepto ,tipo_doc.nombre as tipo_docn ,
	 conceptos.descripcion as id_concepton,detalle_cxc.descripcion ,factura_cxc.fecha, 
         factura_cxc.periodo , factura_cxc.fecha_vencimiento,factura_cxc.fecha_mora ,
         detalle_cxc.tdr as totalr , detalle_cxc.tdo as totalo, detalle_cxc.tcr as pagor, detalle_cxc.tco as pagoo ,
         detalle_cxc.rfc, detalle_cxc.rfd, detalle_cxc.rid , detalle_cxc.ric, detalle_cxc.ivd, detalle_cxc.ivc
	from detalle_cxc, factura_cxc, conceptos, tipo_doc
	  where factura_cxc.id_contrato=  $id 
	  and factura_cxc.tipo_doc = detalle_cxc.tipo_doc    
	  and factura_cxc.id_factura=detalle_cxc.id_factura    
	  and detalle_cxc.tipo_doc = tipo_doc.tipo_doc    
	  and detalle_cxc.id_concepto not in(1,2,9,99)    
	  and detalle_cxc.id_concepto = conceptos.id_concepto     
	  and detalle_cxc.tdr+detalle_cxc.tdo -(detalle_cxc.tcr+detalle_cxc.tco)>0    
	  order by factura_cxc.periodo desc  ";
        
        
        
        
        if($type =="ARR"){
          $r = $this->_conn->_query($q1);
        }
        else
        {
            
             $r = $this->_conn->_query($q2);
        }
        
          $list = array();
          
          while($row = mssql_fetch_array($r)){
              
              $list[] = $row; 
          };
        
          
          
          return $list;
   
        
    }
    
    
    // mapper conceptos
    
    public function mapperconc($conc){
        
        $map = array(
            "COM"=>"302",
            "VIV"=>"301",
            "13"=>"313",
            "21"=>"320",
            "22"=>"320",
            "23"=>"320",
            "24"=>"320",
            "25"=>"320",
            "26"=>"320",
            "27"=>"320",
            "28"=>"320",
            "34"=>"320",
            "31"=>"321", // honorarios
            "32"=>"322", // gastos de demanda
            "38"=>"321",
            
             
            );
        
        
        return $map[$conc];
        
        
        
    }
    
    
    public function getConcOtros($id){
        
        
        $q2=" SELECT [id_contrato]
      ,[id_concepto]
      ,[descripcion]
      ,[año]
      ,[mes]
      ,[abono]
      ,[cargo]
      ,[nfactura]
      ,[fechaf]
      ,[id_user]
      ,[last_date]
      ,[ESTADO]
      ,[ID_USER_R]
      ,[FECHA_R]
      ,[AFAVOR]
      ,[ID_ABOG]
      ,[nkey]
      ,[fecha]
  FROM [log_conceptos]
  where cargo-abono > 0
  and id_contrato = ".$id;
        
        
         $r = $this->_conn->_query($q2);
       
        
          $data = array();
          
          while($row = mssql_fetch_array($r)){
              
              $data[] = $row; 
          };
        
          
        
        
        
        return $data;
    }
    
    
    
    
    public function getCarteraContrato($id)
   {
        
        $dfecha = date('Y-m-d');
        // cargar parametros
        
        $pmulta = $this->_parameters[34]/100;
        $xmontopre = $this->_parameters[61];
        $p_iva_int = $this->_parameters[91]/100;
       
        $imora = $this->_parameters[9]/100;
        $igasto = $this->_parameters[14]/100;
        $diasm = $this->_parameters[28];
        
        // data del contrato
        $con = $this->getContrato($id);
        
        $ec = array("id_contrato"=>$con['id_contrato']);
         
        // traer conceptos pendientes de arriendo y cargar
        
        $conca = $this->getConcPendientes($id, "ARR");
        
        $arr = array();
        
        foreach ($conca as $value ){
            
            $cfecha=date("Y-m-d",strtotime($value['fecha']));
            $cfecha_mora=date("Y-m-d",strtotime($value['fecha_mora']));
           
            $nval = 0;
            
            
            if($dfecha > $cfecha_mora){
                
                $ndias_m = $this->dif30($dfecha,$cfecha_mora);
    
            }else
            {
                $ndias_m=0;
                
            }
            
           
            // calcular intereses por cada concepto
            $ndias_m=($ndias_m>0)?$ndias_m:0;
            
            $ntotfac=$value['salr']+$value['salo'];
	    $nval_int1=round(($ntotfac*$imora*$ndias_m)/30,0);
			//--mas iva
            $nval=round($nval_int1*(1+$p_iva_int),0);
            
            $cconc = ($value['id_concepto']==99)?'CANON':$value['id_concepton'];
            
           
            $cdescripcion =$cconc." ".$this->c_mes(intval(substr($value['periodo'],4,2)),true)."/".substr($value['periodo'],0,4);
		
             // no aplicar intereses a 13 y 133
            if($value['id_concepto']==13 || $value['id_concepto']==133){
                
                $ndias_m=0;
                $nval=0;
                
            }
            
            
            
            $line = array(
                "id_factura"=>$value['id_factura'],
                "tipo_factura"=>$value['tipo_doc'],
                "id_concepto"=>$value['id_concepto'],
                "descripcion"=>$cdescripcion, 
                "pendiente" =>  round($value['salr'] + $value['salo']),
                "tipo_l"=>"D",
                "seccion"=>"ARR",
                "int_mora"=>$nval,
                "fecha"=>$cfecha,
                "fecha_mora"=> $cfecha_mora,
                "texto"=>"ARRENDAMIENTOS",
                "diasmora"=>$ndias_m,
                "iva"=>$value['iva'],
                "conc"=> ($value['iva']>0)?$this->mapperconc('COM'):$this->mapperconc('VIV'),
                "totalr"=>$value['totalr'],
                "totalo"=>$value['totalo'],
                "pagor"=>$value['pagor'],
                "pagoo"=>$value['pagoo'],
              
                
                
            );
            
            array_push($arr,$line); 
            
            if($value['id_concepto'==13]){
                
                
                
            }
            
            
        }
        
        
       $ec['conc_arr']=$arr;
        
        
       // conceptos por facturar 
       
       // cargar otros conceptos
       
       
       $listo_o = $this->getConcOtros($id);
       
       
       $otros = array();
       
       foreach ($listo_o as $value ){
           
           $date=date_create($value['fecha']);
        
           
           
           $line = array( "id_contrato"=> $id,
               "pendiente"=> $value['cargo']-$value['abono'],
               "pagado"=>$value['abono'],
               "total"=> $value['cargo'],
               "descripcion"=>$value['descripcion'],
               "fecha"=>date_format($date,"Y/m/d"),
               "id_concepto"=> $this->mapperconc($value['id_concepto'])
               
               
               );
           
           
             array_push($otros,$line); 
           
           
       }
               
               
        $ec['conc_otros']=$otros;
       
       
       
       
       
       
        
        return $ec;
        
        
        
        
    }
public function dif30($f1,$f2){
       
      //f1-f2

 $f1=strtotime($f1);
$f2=strtotime($f2);
      
     
    if( $f2>$f1) {
            $t=$f1;
            $f1=$f2;
            $f2=$t;
    }


    $dd1=date('d',$f1);
    $mm1=date('m',$f1);
    $aa1=date('Y',$f1);
    $dd2=date('d',$f2);
    $mm2=date('m',$f2);
    $aa2=date('Y',$f2);



    $dd1=($dd1>30)?30:$dd1;
    $dd2=($dd2>30)?30:$dd2;

    $dd1=($mm1=2 && $dd1>=28)?30:$dd1;
    $dd2=($mm2=2 && $dd2>=28)?30:$dd2;

    if( $dd1<$dd2) {
            $dd1=$dd1+30;
            $mm1=$mm1-1;
    }

    if( $mm1<$mm2){
            $mm1=$mm1+12;
            $aa1=$aa1-1;
    }
return ($aa1-$aa2)*360+($mm1-$mm2)*30+($dd1-$dd2);

}
    

public function c_mes($mes,$l){




if( $l ){
	
	switch($mes){
    
             case 1:
		$cmes= 'Enero';
                 break; 
          
		  case 2:
		$cmes='Febrero';
                 break;         
		  case 3:
		$cmes='Marzo';
                 break;  
		  case 4:
		$cmes='Abril';
                 break;  
		  case 5:
		$cmes='Mayo';
                 break;  
		  case 6:
		$cmes='Junio';
                 break;  
		  case 7:
		$cmes='Julio';
                 break;  
		  case 8:
		$cmes='Agosto';
                 break;  
		  case 9:
		$cmes='Septiembre';
                 break;  
		  case 10:
		$cmes='Octubre';
                 break;  
		  case 11:
		$cmes='Noviembre';
                 break;  
		  case 12:
		$cmes='Diciembre';
                 break;  
    }
}
else {
    switch($mes){
    
             case 1:
		$cmes= 'ENE';
                 break; 
          
		  case 2:
		$cmes='FEB';
                 break;         
		  case 3:
		$cmes='MAR';
                 break;  
		  case 4:
		$cmes='ABR';
                 break;  
		  case 5:
		$cmes='MAY';
                 break;  
		  case 6:
		$cmes='JUN';
                 break;  
		  case 7:
		$cmes='JUL';
                 break;  
		  case 8:
		$cmes='AGO';
                 break;  
		  case 9:
		$cmes='SEP';
                 break;  
		  case 10:
		$cmes='OCT';
                 break;  
		  case 11:
		$cmes='NOV';
                 break;  
		  case 12:
		$cmes='DIC';
                 break;  
    }
}
return $cmes;

}


 }
