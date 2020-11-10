<?php

class empleados {
    
    private $_conn;
    
     function __construct(data $conn) {

        $this->_conn = $conn;
    }
    
    
    public function  get($email=null){
        
        $email = is_null($email)?"":" and e.email ='".$email."'";
        
        $q="select  e.iden, e.nombre1 +' '+e.nombre2 as nombre ,"
                . " e.apellido1 +' '+e.apellido2 as apellido, e.email"
                . " from Nm_empleado e , nm_contrato c "
                . " where e.iden = c.iden_empleado "
                . " and c.inactivo = 0"
                . ""
                . "".$email;
        
         $r = $this->_conn->_query($q);
         
                

        return $this->_conn->_getData($r);
            
        
    }
    
    
    
    
    
}
