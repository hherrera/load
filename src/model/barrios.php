<?php

class barrios {
    
    private $_conn;
    
     function __construct(data $conn) {

        $this->_conn = $conn;
    }
    
    
    public function  get($id=null){
        
        $id = is_null($id)?"":"  where  s.id_ciudad ='".$id."'";
        
        $q="SELECT     b.id_barrio, b.nombre AS barrio, b.id_sector,  b.lat, b.lon, s.descripcion
    FROM         barrios AS b INNER JOIN
                      sectores AS s ON b.id_SECTOR = s.id_sector ".$id;
        
         $r = $this->_conn->_query($q);
         
                

        return $this->_conn->_getData($r);
        
    }


    public function getJson($id=null){

      $barrios=$this->get($id);
    
   
      
      $res = array();
    foreach ($barrios as $key => $value) {
    
        $row = array(
                'id_barrio'=>  $value['id_barrio'],
                'barrio'=>$value['barrio'],
              'id_sector'=>$value['id_sector'],
                'sector'=>$value['descripcion'],
                 'lat'=>$value['lat'],
                 'lon'=>$value['lon']

            );
        
    

        
        
        $res[]= $row;
    }


    
    $json = json_encode($res);
    
    
    return $json;
    

    }
}

?>



    