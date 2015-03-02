<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class cartera {

    private $_conn;

    function __construct(data $conn) {

        $this->_conn = $conn;
    }

    public function getCobradoresEspecial() {

        return array('000', '009');
    }

    public function getCobradoresActivos() {





        return array('003', '006', '008');
    }

    public function getCobradoresJuridico() {


        $lstCobJur = array('004');

        return $lstCobJur[0];
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
and TIPO_DOC IN(3,13,20) ";
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

        $q = "select l.id from lstcobro l where l.ano=" . $periodo['year'] . " and l.mes = " . $periodo['month'];
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

        if (($estado == "3" ) && $result['saldo'] <= 0) {
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
        } else if ($mora> 61 && $co['act_juridico'] == 0 && $co['estado'] == 2) {
            $label = "Juridica";
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

    public function getCobradoresInactivo() {

        return '991';
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

        $cob_especial = $this->getCobradoresEspecial();

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
                $id_cobrador = trim($this->getCobradoresInactivo());
            } else {

                echo "\n  1. ";
                // validar reglas de clasificacion
                //  juridico
                if (($diasmora > 61 && $co['act_juridico'] == 0) || ($co['act_juridico'] == 1)) {

                    echo "\n  2. juridico";
                    $id_cobrador = trim($this->getCobradoresJuridico());
                }

                if (($diasmora <= 61 && $co['act_juridico'] == 0)) {
                    echo "\n  3. 0-60";
                    $periodo = array('year' => $lst['ano'], 'month' => $lst['mes']);

                    // dejar el mismo si cobrador actual es activo

                    $cob_activos =$this->getCobradoresActivos();

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

    public function getUserCob($id) {

        $q = "select c.usuario as id_user from cobradores c where c.id_cobrador = '$id' ";
        $r = $this->_conn->_query($q);

        $row = mssql_fetch_array($r);
        return trim($row['id_user']);
    }

    public function getCobMin($lst = array(), $periodo) {


        $lstCobAct = array('003', '006', '008');


        $str_cob = implode("','", $lstCobAct);

        $q = "select sum(l.canon+l.admi)  as N , l.id_cobrador from contratos l"
                . " where l.id_cobrador in('" . $str_cob . "') "
                . " and l.estado=2"
                . " group by l.id_cobrador order by sum(l.canon+l.admi) asc";




        $r = $this->_conn->_query($q);

        $row = mssql_fetch_array($r);

        return trim($row['id_cobrador']);
    }

    public function getConc() {

        $string = file_get_contents("/var/www/load/conf/conc.json");


        $json_a = json_decode($string, true);

        return $json_a;
    }

    public function actLstCob($periodo) {

        $q = "select  id from lstCobro where ano=" . $periodo['year'] . " and "
                . " mes = " . $periodo['month'] . " order by id asc ";



        $r = $this->_conn->_query($q);



        while ($row = mssql_fetch_array($r)) {


            echo $this->setLabel($row['id']);
            echo "\n ";    
            echo $this->setCobrador($row['id']);
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

}
