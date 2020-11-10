<?php

class preguntas {
    
    private $_conn;
    
     function __construct(data $conn) {

        $this->_conn = $conn;
    }
    
    
    public function  getAllPreguntas(){
        
        
        $q="select  * from preguntas ";
        
         $r = $this->_conn->_query($q);
         
                

        return $this->_conn->_getData($r);
            
        
    }
    
    
    
    
    
}
