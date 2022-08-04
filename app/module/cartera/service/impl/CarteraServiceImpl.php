<?php

class CarteraServiceImpl implements CarteraService {

    private $carteraDao;

    function __construct() {
        $this->carteraDao = new CarteraDaoImpl();
    }


    public function getPuntos($idUsuario) {    
        $response = new WSResponse();
        $demos =    $this->carteraDao->getPuntos($idUsuario);
       
        if($demos){
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($demos[0]["puntosdisponibles"]);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function setPuntosToUser($idUsuario, $puntos){
        $response = new WSResponse();

        //obtenemos puntos actuales
        $demos =  $this->carteraDao->getPuntos($idUsuario);
        $puntosActuales =0;
       
        $isNew= true;
        if ($demos) {
            $puntosActuales =   (int) $demos[0]["puntosdisponibles"];
         
            $isNew= false;

        }
        $puntosAAgregar = $puntosActuales + $puntos;


        //guardamos puntos actualizados
        $demos =  $this->carteraDao->setPuntos($idUsuario, $puntosAAgregar, $isNew);


        if($demos){
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($demos);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();

    }

    public function getAbonos($idUsuario) {    
        $response = new WSResponse();
        $ordenCompra =  $this->carteraDao->getAbonos($idUsuario);

        $ordenCompraResponse = array();

        
        foreach ($ordenCompra as $key => $value) {                         
            $ordenCompraItem = array(
                "idordercompra" => $value["idordercompra"],
                "totalcompra" => $value["totalcompra"],
                "idevento" => $value["idevento"],
                "fechaalta" => $value["fechaalta"],
                "nombreevento" => $value["nombreevento"],
                "numboletoscompra" => $value["numboletoscompra"],
                "estatusordenCompra" => $value["estatusordencompra"],
                "abonos" => $this->carteraDao->getAbonosPago($value["idordercompra"])
            );
            array_push($ordenCompraResponse, $ordenCompraItem);
        }
        
        if($ordenCompraResponse){
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($ordenCompraResponse);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }
}