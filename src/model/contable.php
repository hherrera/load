<?php


class contable {
    
    private $_conn;
    
     function __construct(data $conn) {

        $this->_conn = $conn;
    }
    
    
    
    
    public function  getDocument(array $param){
    
        
        ## Analizar parametros
        
        if(array_key_exists('ANODCTO',$param) && array_key_exists('FNTEDCTO',$param) ) {
     
            $query ="SELECT     d.ANODCTO, d.FNTEDCTO, d.NUMEDCTO, d.FECHDCTO, d.DESCDCTO,
                          (SELECT     NOMBRETER
                            FROM          TERCEROS
                            WHERE      (IDTERCERO = d.IDTERCERO)) AS tercero, d.IDTERCERO, f.DESFUENTE
                            FROM         [DOCUMENT] AS d INNER JOIN
                      FUENTES AS f ON d.FNTEDCTO = f.IDFUENTE
                       WHERE     (d.ANODCTO = '".$param['ANODCTO']."') "
                    . "AND (d.FNTEDCTO = '".$param['FNTEDCTO']."')";
            
                
        
        }elseif(array_key_exists('ANODCTO',$param))
         {
           
           $query ="SELECT     d.ANODCTO, d.FNTEDCTO, d.NUMEDCTO, d.FECHDCTO, d.DESCDCTO,
                          (SELECT     NOMBRETER
                            FROM          TERCEROS
                            WHERE      (IDTERCERO = d.IDTERCERO)) AS tercero, d.IDTERCERO, f.DESFUENTE
                            FROM         [DOCUMENT] AS d INNER JOIN
                      FUENTES AS f ON d.FNTEDCTO = f.IDFUENTE
                       WHERE     (d.ANODCTO = '".$param['ANODCTO']."') "
                  ;
            
        }elseif(array_key_exists('NUMEDCTO',$param) && array_key_exists('FNTEDCTO',$param)){
            
           $query ="SELECT     d.ANODCTO, d.FNTEDCTO, d.NUMEDCTO, d.FECHDCTO, d.DESCDCTO,
                          (SELECT     NOMBRETER
                            FROM          TERCEROS
                            WHERE      (IDTERCERO = d.IDTERCERO)) AS tercero, d.IDTERCERO, f.DESFUENTE
                            FROM         [DOCUMENT] AS d INNER JOIN
                      FUENTES AS f ON d.FNTEDCTO = f.IDFUENTE
                       WHERE     (d.NUMEDCTO = '".$param['NUMEDCTO']."') "
                        . "AND (d.FNTEDCTO = '".$param['FNTEDCTO']."')";
                  ;
            
        }elseif(array_key_exists('YEAR',$param)) {
     
            $query ="SELECT     d.ANODCTO, d.FNTEDCTO, d.NUMEDCTO, d.FECHDCTO, d.DESCDCTO,
                          (SELECT     NOMBRETER
                            FROM          TERCEROS 
                            WHERE      (IDTERCERO = d.IDTERCERO)) AS tercero, d.IDTERCERO, f.DESFUENTE
                                                     FROM         [DOCUMENT] d INNER JOIN
                      FUENTES AS f ON d.FNTEDCTO = f.IDFUENTE
                            WHERE     (substring(d.ANODCTO,1,4) = '".$param['YEAR']."') "
                  ;
            
                
        
        }
        
        
        
        $r = $this->_conn->_query($query);
         
                

        return $this->_conn->_getData($r);
        
    }

    
     public function  getTransac(array $param){
    
         
         
           $query ="SELECT     ANOTRA, IDFUENTE, NUMDOCTRA, CONSECUTRA, FECHATRA, CODICTA, NITTRA, AUXIAUX, IDCENCO, IDITEM, DESCRITRA, VALORTRA, INDCPITRA, CONCILTRA, 
                      IDBANCO, IDVENDE, IDPLAZA, TIPOFAC, NUMEFAC, VENCEFAC, REFEFAC, IDUSUARIO, FGRATRA, IDZONA, CLIPRV, PORRETETRA, BASERETETRA, CODPRESU, 
                      NRESERVA, VALORMONEDA, STATUSTRA, CONSECUREV, SERIE, AUTORIZACION, FECHAFACT, Adicional_1, Adicional_2, Voucher, TasaCambio, BU, NCF, 
                      NCF_Modificado, FechaCaducidad
                        FROM         TRANSAC
                        WHERE     (ANOTRA = '".$param['ANOTRA']."') "
                   . "AND (NUMDOCTRA = '".$param['NUMDOCTRA']."') "
                   . "AND (IDFUENTE = '".$param['IDFUENTE']."')";
                      
         
         
         $r = $this->_conn->_query($query);
         
        return $this->_conn->_getData($r);
         
         
     }
     
}