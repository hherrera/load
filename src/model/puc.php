<?php

class cuenta {
    
    private $_conn;
    
     function __construct(data $conn) {

        $this->_conn = $conn;
    }
    
    
    public function  get($id=null){

        $id = is_null($id)?"":"  AND codicta ='".$id."'";

        $q="SELECT     CODICTA, DESCCTA, NIVELCTA, TIPOCTA, CODDCTA, CATFCTA, FEAPCTA, INDPG3CTA, INDCPICTA, IDBANCO, INDCCOCTA, CIERRECTA, AJUINFCTA, CONINFCTA, 
                              NCDPGCTA, UNIADIC1, CODIAJU1, UNIADIC2, CODIAJU2, UNIADIC3, CODIAJU3, INDUNCAL, FORMUCAL, CONTROLPRESU, DISTRIBUCION, IDMONEDA, CODIAJUSTE, 
                              PORCEIMPUESTO, DATOSIMPUESTOS, CTACORRIENTE, IDENTICTA, CIERRE3ROCTA, HABILITARCTA, NATURALEZACTA, GRUPO, TasaAjuste, IndBaseP0, IndNCF, 
                              NIVELPARAMAYOR, CtaAjusteMonPerdida, ExigeItem
        FROM         MAECONT
        WHERE     (NIVELCTA < 5) ".$id;

     
        
        
         $r = $this->_conn->_query($q);
         
                

        return $this->_conn->_getData($r);
        
        
        
    }
   
    
    
     public function getJson($id=null){

      $data=$this->get($id);

      
       return json_encode($data);
    

    }

    
    
}