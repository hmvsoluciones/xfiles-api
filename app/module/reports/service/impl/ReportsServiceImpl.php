<?php

class ReportsServiceImpl implements ReportsService {

    private $reportsDao;

    function __construct() {
        $this->reportsDao = new ReportsDaoImpl();
    }

    
    public function getBoletosVendidos($id){        
        return $this->reportsDao->getBoletosVendidos($id);
    }

    public function getHTMLTableReport($sql){
        return $this->reportsDao->getHTMLTableReport($sql);
    }
    public function serchOrdenCompra($ordenCompra){
        $responseObj = $this->reportsDao->serchOrdenCompra($ordenCompra);
        $response = new WSResponse();
       
        if ($responseObj) {
            $response->setSuccess(true);
            $response->setMessage("Registro obtenido correctamente");
            $response->setObject($responseObj);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al obtener los registros");
            $response->setObject(null);
        }
        return $response->expose();
    }

   
}