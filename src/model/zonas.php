<?php

class zonas {
    
    private $_conn;
    
     function __construct(data $conn) {

        $this->_conn = $conn;
    }
    
    
    public function  get($id=null){
        
        $id = is_null($id)?"":"  where s.id_ciudad ='".$id."'";
        
        $q="SELECT  s.id_sector, s.descripcion as sector from sectores s ".$id        ;
        
         $r = $this->_conn->_query($q);
         
                

        return $this->_conn->_getData($r);
            
        
    }


    public function getJson($id=null){

      $zonas=$this->get($id);

      $res = array();
    foreach ($zonas as $key => $value) {
    
   
        $row = array(
                'id_sector'=>$value['id_sector'],
                'sector'=>$value['sector']
                )   ;
        
        $res[]= $row;
    }

    return json_encode($res);
    

    }

}

?>