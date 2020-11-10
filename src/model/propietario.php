<?php

class propietario {

 private $_conn;
 private $_id;

 private $_data = array('ndoc'=>null , 
 	'fecha'=>null ,
 	'tipo_doc'=>null ,  
 	'description'=>null,
 	'id_inmueble'=>null,
 	'id_concepto'=>null , 
 	'db'=>null ,
 	'cr'=>null ,
 	'l'=>null ,           
 	);
    

function __construct (data $conn, $id ){
      
        $this->_conn=$conn;
        $this->_id=$id;
 }

public function getSaldo($per12, $lflag){

$afav=0;
$enc=0;
$afava=0;
$impua=0;
$enca=0;
$pag_act=0;
$impuan=0;
$pag_ant=0;

$q="select sum(valor_cr) as afav , sum(comision+iva-ret_comi) as impuan 
from factura_cxp where periodo<'$per12' 
and tipo_doc<>2 and id_convenio=$this->_id";

$r=$this->_conn->_query($q);
$row = mssql_fetch_array($r);
$impuan=$row['impuan'];
$afav=$row['afav'];




$q="select  sum(valor_db) as pag_ant 
from factura_cxp where periodo<'$per12' 
and tipo_doc<>2 and id_convenio=$this->_id 
and tipo_doc in(10,22)";


$r=$this->_conn->_query($q);
$row2 = mssql_fetch_array($r);
$pag_ant = $row2['pag_ant'];
$pag=$impuan+$pag_ant;




$q="select sum(valor_db) as enc from cxc_propietarios where periodo<'$per12' 
 and tipo_doc<>2 and id_convenio=$this->_id";
$r=$this->_conn->_query($q);
$row3 = mssql_fetch_array($r);
$enc=$row3['enc'];

 
  
  $nintmora=0;
  
  if($lflag==true){
  	$q="select sum(valor_db) as nintmora from cxc_propietarios where periodo<'$per12' 
	 and tipo_doc<>2 and id_convenio=$this->_id 
	 and id_concepto in(80,81)  AND fecha >='2011-01-01'";
	$r=$this->_conn->_query($q);
	$row3 = mssql_fetch_array($r);
	$nintmora=$row3['nintmora'];
	$enc=$enc-$nintmora;

	var_dump($row3);
  }
    

//-------Calcula los movimientos creditos actuales--------------------------------------------*  
  
  	$q="select sum(valor_cr) as afava , sum(comision+iva-ret_comi) as impua 
  	 from factura_cxp where 
  	periodo='$per12' and tipo_doc<>2 and id_convenio=$this->_id";
  	$r=$this->_conn->_query($q);
	$row = mssql_fetch_array($r);
	$afava = $row['afava'];
	$impua = $row['impua'];

$q="select sum(valor_db) as pag_act 
  	 from factura_cxp where 
  	periodo='$per12' and tipo_doc In(10,22) and id_convenio=$this->_id";
  	$r=$this->_conn->_query($q);
	$row= mssql_fetch_array($r);
	$pag_act = $row['pag_act'];

	$q="select sum(valor_db) as enca from cxc_propietarios where periodo='$per12'
	 and tipo_doc<>2 and id_convenio=$this->_id";
	$r=$this->_conn->_query($q);
	$row = mssql_fetch_array($r);
	$enca = $row['enca'];

 $saldo=($afav-$enc-$pag)+($afava-$enca-$impua)-$pag_act;


return round($saldo,4);



}


public function getProperData(){


	$q=" select LTRIM(RTRIM(apellido)+SPACE(1)+RTRIM(nombre)) as nombre,
	id_cliente,
	 LTRIM(RTRIM(barrio_co)+SPACE(1)+ RTRIM(dir_co)+SPACE(1)+RTRIM(edificio_co)) as dir_residencia 
	 ,ciudad_co as ciudad_residencia 
	 from clientes where id_cliente in 
    (select id_cliente from clientes_convenios 
     where id_convenio=".$this->_id." and tipo_relacion='P')";
	$r=$this->_conn->_query($q);
	$data = mssql_fetch_array($r);



	return $data;
}


public function getConc_Comi($periodo){

	$data = $this->_data;

	$q="select sum(comision) as comision , sum(iva) as iva, sum(ret_comi ) as ret_comi
	 from factura_cxp,tipo_doc 
 	where factura_cxp.tipo_doc<>2 
 	and factura_cxp.periodo='".$periodo."'
 	and factura_cxp.id_convenio=".$this->_id."
 	and factura_cxp.COMISION>0
 	and factura_cxp.tipo_doc=tipo_doc.tipo_doc ";

	$r=$this->_conn->_query($q);
	$row = mssql_fetch_array($r);
	$comision = $row['comision'];
	$iva= $row['iva'];
	$ret_comi = $row['ret_comi'];


	$q="select f.id_documento,f.fecha, f.tipo_doc,f.descripcion,
	f.id_inmueble,f.id_concepto,f.valor_db, f.valor_cr
	,tipo_doc.nombre2 AS nombretipodoc from factura_cxp f,tipo_doc 
	 where f.tipo_doc<>2 
	 and f.periodo='".$periodo."'
	 and f.id_convenio=".$this->_id."
	 and f.COMISION>0
	 and f.tipo_doc=tipo_doc.tipo_doc 
	 order by f.fecha,f.descripcion ";

	$r=$this->_conn->_query($q);
	



	while($row2 = mssql_fetch_array($r)){

		$data[]= array( 'ndoc'=>$row2['id_documento'] , 
 	'fecha'=>date('Y-m-d',strtotime($row2['fecha'])) ,
 	'tipo_doc'=>$row2['nombretipodoc'],  
 	'id_tipo_doc'=>$row2['tipo_doc'],
 	'description'=>$row2['descripcion'],
 	'id_inmueble'=>$row2['id_inmueble'],
 	'id_concepto'=>$row2['id_concepto'] , 
 	'db'=>0 ,
 	'cr'=>$row2['valor_cr'] ,
 	'l'=>date('d',strtotime($row2['fecha'])) ,           
 	);
    


	}

	return $data;


}


public function getConc_NoComi($periodo){

	$data = $this->_data;

	
	$q="select f.id_documento,f.fecha, f.tipo_doc,f.descripcion,
	f.id_inmueble,f.id_concepto,f.valor_db, f.valor_cr
	,tipo_doc.nombre2 AS nombretipodoc from factura_cxp f,tipo_doc 
	 where f.tipo_doc<>2 
	 and f.periodo='".$periodo."'
	 and f.id_convenio=".$this->_id."
	 and f.COMISION= 0 and (f.valor_cr>0 OR f.VALOR_DB>0)
	 and f.tipo_doc=tipo_doc.tipo_doc 
	 order by f.fecha,f.descripcion ";

	$r=$this->_conn->_query($q);
	
	$comision1= 0;
	$iva1=0;


	while($row2 = mssql_fetch_array($r)){



		if( !in_array($row2['id_concepto'], array(48,49,53)) && $row2['valor_cr']>0  ){

			$data[]= array( 'ndoc'=>$row2['id_documento'] , 
				 	'fecha'=>date('Y-m-d',strtotime($row2['fecha'])) ,
				 	'tipo_doc'=>$row2['nombretipodoc'],
				 	 'id_tipo_doc'=>$row2['tipo_doc'],
				 	'description'=>$row2['descripcion'],
				 	'id_inmueble'=>$row2['id_inmueble'],
				 	'id_concepto'=>$row2['id_concepto'] , 
				 	'db'=>0 ,
				 	'cr'=>$row2['valor_cr'] ,
				 	'l'=>date('d',strtotime($row2['fecha'])) ,           
 					);

		}

		if($row2['id_concepto'] == 48){

			$comision1 =$comision1+$row2['valor_cr'];
			
		}
		
		if($row2['id_concepto'] == 49){

			$iva1 =$iva1+$row2['valor_cr'];
			
		}
		

		if($row2['id_concepto'] == 53 && in_array($row2['tipo_doc'], array(10,22) ) ){

				$data[]= array( 'ndoc'=>$row2['id_documento'] , 
					 	'fecha'=>date('Y-m-d',strtotime($row2['fecha'])) ,
					 	'tipo_doc'=>$row2['nombretipodoc'],  
					 	'id_tipo_doc'=>$row2['tipo_doc'],
					 	'description'=>$row2['descripcion'],
					 	'id_inmueble'=>$row2['id_inmueble'],
					 	'id_concepto'=>$row2['id_concepto'] , 
					 	'db'=>$row2['valor_db'] ,
					 	'cr'=>0 ,
					 	'l'=>60 ,           
					 	);


		} elseif ( $row2['id_concepto'] == 53 && !in_array($row2['tipo_doc'], array(10,22))){

					$data[]= array( 'ndoc'=>$row2['id_documento'] , 
					 	'fecha'=>date('Y-m-d',strtotime($row2['fecha'])) ,
					 	'tipo_doc'=>$row2['nombretipodoc'],  
					 	'id_tipo_doc'=>$row2['tipo_doc'],
					 	'description'=>$row2['descripcion'],
					 	'id_inmueble'=>$row2['id_inmueble'],
					 	'id_concepto'=>$row2['id_concepto'] , 
					 	'db'=>0 ,
					 	'cr'=>$row2['valor_cr'] ,
					 	'l'=>date('d',strtotime($row2['fecha'])) ,           
					 	);

		}


	}


	return array('comision1'=>$comision1 ,'iva1'=>$iva1,'data'=>$data);


}



public function getCargosProp($periodo){
 	$data =array(); 
	$ret_comi1=0;
		$q=" select cxc_propietarios.*,tipo_doc.nombre2 AS NOMBRE
	  from cxc_propietarios,tipo_doc 
	 where cxc_propietarios.tipo_doc<>2
	 and cxc_propietarios.periodo=".$periodo."
	 and cxc_propietarios.id_convenio=". $this->_id."
	 and (cxc_propietarios.valor_db>0 or cxc_propietarios.valor_cr>0)
	 and cxc_propietarios.id_Concepto not IN(6,7)
	 and cxc_propietarios.tipo_doc=tipo_doc.tipo_doc
	 order  by cxc_propietarios.fecha,cxc_propietarios.descripcion ";

	$r=$this->_conn->_query($q);
	while($row2 = mssql_fetch_array($r)){
		if($row2['id_concepto']==50){
			$ret_comi1+= $row2['valor_db'];
		}else
		{

			$data[]= array( 'ndoc'=>$row2['id_documento'] , 
					 	'fecha'=>date('Y-m-d',strtotime($row2['fecha'])) ,
					 	'tipo_doc'=>$row2['nombretipodoc'],  
					 	'id_tipo_doc'=>$row2['tipo_doc'],
					 	'description'=>$row2['descripcion'],
					 	'id_inmueble'=>$row2['id_inmueble'],
					 	'id_concepto'=>$row2['id_concepto'] , 
					 	'db'=>0 ,
					 	'cr'=>$row2['valor_cr'] ,
					 	'l'=>date('d',strtotime($row2['fecha'])) ,           
					 	);

		}


	}


	return array('ret_comi1'=>$ret_comi1 ,'data'=>$data);
}




	
}


?>


