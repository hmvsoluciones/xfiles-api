<?php

class BoletosServiceImpl implements BoletosService {

    private $boletosDao;
    private $util;
    private $openPayDao;

    function __construct() {
        $this->boletosDao = new BoletosDaoImpl();
        $this->util = new UtilImpl();
        $this->openPayDao = new OpenPayDaoImpl();
    }
    
    public function loadKardexBoletos($idEvento, $idTipoBoleto, $cantidadBoletos, $nombre, $correo, $telefono,  $facebook, $instagram, $estatus){
       $listaIdKardex = array();
        for ($i=0; $i < $cantidadBoletos ; $i++) { 
            
            $folios = $this->boletosDao->generateFolio($idEvento);

            $inserted = $this->boletosDao->loadKardexBoletos($idTipoBoleto, $folios["folio"], $folios["asiento"], $nombre, $correo, $telefono, $facebook, $instagram, $estatus);
            
            array_push($listaIdKardex, $inserted);
        }
        return $listaIdKardex;
    }

    public function addBoletosFisicos($numeroBoletosImpresos, $idUsuario, $idTipoBoleto, $idEvento){ 
        $response = new WSResponse();
        try {

            $this->boletosDao->beginTransaction();
            $ocupacion = $this->boletosDao->getCupoTipoBoleto($idTipoBoleto);
            $boletosCreadosEvento=$this->boletosDao->getBoletosCreadosTipoBoleto($idTipoBoleto);
            $validaboletos= $boletosCreadosEvento + $numeroBoletosImpresos;
            
            if($ocupacion < $validaboletos){
                throw new Exception('Se estaria excediendo la cantidad de boletos para este tipo');
            }


            $idBoletosFisicos = $this->boletosDao->loadBoletosFisicos($numeroBoletosImpresos, $idUsuario, $idTipoBoleto);
            
            if(!$idBoletosFisicos){
                throw new Exception('No se logro crear el registro de boletos fisicos');
            }                                                        
            $boletosCreados  = $this->loadKardexBoletos($idEvento, $idTipoBoleto, $numeroBoletosImpresos,"Boleto Fisico", null, null,  null, null, 3);
            if(count($boletosCreados) <= 0){
                throw new Exception('No se lograron crear los boletos');
            }
               
            foreach ($boletosCreados as $key => $idKardexBoletos) {
                if(!$this->boletosDao->loadFisicoKardex($idBoletosFisicos, $idKardexBoletos)){
                    throw new Exception('No se lograron ligar los boletos');
                }
            }

            $this->boletosDao->commitTransaction();

            $response->setSuccess(true);
            $response->setMessage("Boletos fisicos creados correctamente");
            $response->setObject(null);

        } catch(Exception $e){

            $this->boletosDao->rollBackTransaction();

            $response->setSuccess(false);
            $response->setMessage("No fue posible crear los boletos fisicos: ".$e->getMessage());
            $response->setObject(null);
        }
        return $response->expose();
    }
    
    public function addCortesias(
        $nombrePersonaCortesia, 
        $correoPersonaCortesia, 
        $telefonoPersonaCortesia, 
        $numeroBoletos, 
        $motivoCortesia,
        $idTipoBoleto, 
        $idUsuAlta,
        $idEvento
    ) {
        $response = new WSResponse();
        try {

            $this->boletosDao->beginTransaction();
            $ocupacion = $this->boletosDao->getCupoTipoBoleto($idTipoBoleto);
            $boletosCreadosEvento=$this->boletosDao->getBoletosCreadosTipoBoleto($idTipoBoleto);
            $validaboletos= $boletosCreadosEvento + $numeroBoletos;
            
            if($ocupacion < $validaboletos){
                throw new Exception('Se estaria excediendo la cantidad de boletos para este tipo');
            }

            $idCortesia = $this->boletosDao->loadCortesia($nombrePersonaCortesia, $correoPersonaCortesia, $telefonoPersonaCortesia, $numeroBoletos,$motivoCortesia, $idTipoBoleto, $idUsuAlta);
            
            if(!$idCortesia){
                throw new Exception('No se logro crear la cortesia');
            }
                                                       
            $boletosCreados  = $this->loadKardexBoletos($idEvento, $idTipoBoleto, $numeroBoletos, "Cortesia", null, null, null, null, 6/*Cortesias*/);
                       
            if(count($boletosCreados) <= 0){
                throw new Exception('No se lograron crear los boletos');
            }
   
            foreach ($boletosCreados as $key => $idKardexBoletos) {                
                if(!$this->boletosDao->loadCortesiaKardex($idCortesia, $idKardexBoletos)){
                    throw new Exception('No se lograron ligar los boletos');
                }
            }

            $params["subject"] = "Cortesias TicketApp";
            $params["from"] = "servicio@ticketapp.com";
            $params["fromName"] = "Ticket App";
            $params["toArray"] = $correoPersonaCortesia;            
            $tipo = "cortesias";
            
            $urlKey = $this->generateUrlKey($tipo, $idTipoBoleto, $idCortesia);
            $urlBase = URL_BASE;
            $params["body"] = "Descarga tu cortesia: <a href='{$urlBase}document/boletos/?key={$urlKey}'>Descargar Cortesias </a>";
            $this->util->sendMail($params);

            $this->boletosDao->commitTransaction();

            $response->setSuccess(true);
            $response->setMessage("Cortesia creada correctamente");
            $response->setObject(null);

        } catch(Exception $e){

            $this->boletosDao->rollBackTransaction();

            $response->setSuccess(false);
            $response->setMessage("No fue posible crear la cortesia: ".$e->getMessage());
            $response->setObject(null);
        }
        return $response->expose();
    }
    public function getBoletosPrecioPublico($idUsuario){
        $response = new WSResponse();
        $data =  $this->boletosDao->getBoletosPrecioPublico($idUsuario);        
       

        if(count($data) > 0){
            $response->setSuccess(true);
            $response->setMessage("Registro obtenido correctamente");
            $response->setObject($data[0]);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener el registro");
            $response->setObject(null);
        }
        return $response->expose();
    }
    public function getBoletosFisicos($idTipoBoleto) {
        $response = new WSResponse();
        $data =  $this->boletosDao->getBoletosFisicos($idTipoBoleto);        
        
        $dataResponse = array();

       

        foreach ($data as $key => $value) {
            
            $tipo = "fisicos";
            
            $urlKey = $this->generateUrlKey($tipo, $value["idtipoboleto"], $value["idboletosfisicos"]);

            $item = array(
              "fecha" => $value["fecha"],
              "correousu" => $value["correousu"],
              "numboletosimpresos" => $value["numboletosimpresos"],
              "urlkey" => $urlKey
            );

            array_push($dataResponse, $item);
        }
     
        if($dataResponse && count($dataResponse) > 0){
            $response->setSuccess(true);
            $response->setMessage("Registro obtenido correctamente");
            $response->setObject($dataResponse);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener el registro");
            $response->setObject(null);
        }
        return $response->expose();
    }
    public function addListaInvitados($nombreInvitadoPrincipal, $telefonoInvitadoPrincipal, $correoInvitadoPrincipal,
        $numeroBoletosDisponibles, $idTipoBoleto, $urlGenBoletos,$idUsuAlta, $idEvento ) {
        $response = new WSResponse();
        
        try {

            $this->boletosDao->beginTransaction();
            $ocupacion = $this->boletosDao->getCupoTipoBoleto($idTipoBoleto);
            $boletosCreadosEvento=$this->boletosDao->getBoletosCreadosTipoBoleto($idTipoBoleto);
            $validaboletos= $boletosCreadosEvento + $numeroBoletosDisponibles;
            
            if($ocupacion < $validaboletos){
                throw new Exception('Se estaria excediendo la cantidad de boletos para este tipo');
            }
            
            $idListaInvitados = $this->boletosDao->loadListaInvitados($nombreInvitadoPrincipal, $telefonoInvitadoPrincipal, $correoInvitadoPrincipal,$numeroBoletosDisponibles, $idTipoBoleto, $urlGenBoletos,$idUsuAlta);
                $urlenviolista=URL_BASE."public/lista/?key={$this->util->encrypt($idListaInvitados)}";
                if(!$idListaInvitados){
                    throw new Exception('No fue posible crear la lista de invitados');
                }
               
                $boletosCreados  = $this->loadKardexBoletos($idEvento, $idTipoBoleto, $numeroBoletosDisponibles, null, null, null,  null, null, 5/*Lista invitados*/);
                    foreach ($boletosCreados as $key => $idKardexBoletos) {
                        $this->boletosDao->loadListaInvitadosKardex($idListaInvitados, $idKardexBoletos);
                    }
                    if(count($boletosCreados) > 0){
                        $params["subject"] = "Lista de invitados - complementar información de invitados";
                        $params["from"] = "servicio@ticketapp.com";
                        $params["fromName"] = "Ticket App";
                        $params["toArray"] = $correoInvitadoPrincipal;
                        $params["body"] = "Para descargar sus boletos es necesario  ingresar la información de la lista de invitados completa en la siguiente liga: <b><a href='{$urlenviolista}' _target='blank' >Lista de invitados </a></b><br /> <b> una vez que ingrese la información, se enviara un correo a cada uno de ellos para la descarga del boleto, por lo que es necesario verifique que el correo es el correcto, de lo contrario no podrán obtenerlo</b>";
                        $this->util->sendMail($params);

                    }else {
                        throw new Exception('No fue posible crear los boletos');
                    }    

            $this->boletosDao->commitTransaction();

            $response->setSuccess(true);
            $response->setMessage("Lista invitados creada correctamente");
            $response->setObject(null);

        } catch(Exception $e){

            $this->boletosDao->rollBackTransaction();

            $response->setSuccess(false);
            $response->setMessage("No fue posible crear la Lista de invitados: ".$e->getMessage());
            $response->setObject(null);
        }

        return $response->expose();
    }
 
    private function generateUrlKey($tipo, $idTipoBoleto, $idReferencia) {
        $key = "{$tipo}-{$idTipoBoleto}-{$idReferencia}";
        return $this->util->encrypt($key);
    }

    public function getBoletosList($tipo, $idTipoBoleto, $idReferencia){
        $listaKardex = array();

        if($tipo == "fisicos") {
            $listaKardex = $this->boletosDao->getListaKardexBoletosFisicos($idTipoBoleto, $idReferencia);
        } else if($tipo == "lista"){ 
            $listaKardex = $this->boletosDao->getListaKardexBoletosLista($idTipoBoleto, $idReferencia);
        } else if($tipo == "cortesias"){
            $listaKardex = $this->boletosDao->getListaKardexBoletosCortesia($idTipoBoleto, $idReferencia);
        } else if($tipo = "compra"){            
            $listaKardex = $this->boletosDao->getListaKardexOrdenCompra($idReferencia);
        }

        return $listaKardex;
    }
    public function getAllListaInvidatosByEvento($idEvento) {
        $response = new WSResponse();
        $lista =    $this->boletosDao->getAllListaInvidatosByEvento($idEvento);
        
        $listaResponse = array();
        foreach ($lista as $key => $value) {
            $keyLista = $this->util->encrypt($value["idlistainvitados"]);

            array_push($listaResponse, array("idlistainvitados" => $value["idlistainvitados"],
            "nombreinvitadoprincipal" => $value["nombreinvitadoprincipal"],
            "telefonoinvitadoprincipal" => $value["telefonoinvitadoprincipal"],
            "correoinvitadoprincipal" => $value["correoinvitadoprincipal"],
            "instagraminvitadoprincipal" => $value["instagraminvitadoprincipal"],
            "facebookinvitadoprincipal" =>$value["facebookinvitadoprincipal"],
            "tipoboleto" =>$value["tipoboleto"],
            "numeroboletosdisponibles" => $value["numeroboletosdisponibles"],
            "horafingratuito"=>$value["horafingratuito"],
            "horafinmedioboleto"=>$value["horafinmedioboleto"],
            "urlgenboletos" => $value["urlgenboletos"],
            "urllista" => URL_BASE."public/lista/?key={$keyLista}"
        ));
        }
        if($lista){
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($listaResponse);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }
   
    public function getTipoBoletos($idEvento) {
        $response = new WSResponse();
        $demos =    $this->boletosDao->getalltipoboletos($idEvento);
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

    public function getTipoBoletosDisponibles($idEvento) {
        $response = new WSResponse();
        $tiposBoletos =    $this->boletosDao->getalltipoboletos($idEvento);

        foreach ($tiposBoletos as &$tipoBoleto) {
           $maximoDeBoletos=  $this->boletosDao->getMaximoBoletos($tipoBoleto['id']) ;

           $boletosNoDisponibles = $this->boletosDao->getBoletosInKardexByTipoBoleto($tipoBoleto['id']);
        
           
           $tipoBoleto['boletosDisponibles'] = $maximoDeBoletos - $boletosNoDisponibles;
           
           
        }




        if($tiposBoletos){
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($tiposBoletos);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function getInicioEvento($idEvento) {
        $response = new WSResponse();
        $demos =    $this->boletosDao->getallInicioEvento($idEvento);
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


    //Boletos vendidos detalle 
    public function getBoletosVendidos($idEvento) {
        $response = new WSResponse();
        $demos =    $this->boletosDao->getBoletosVendidos($idEvento);
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
    //Boletos escaneados detalle 
    public function getBoletosEscaneados($idEvento) {
        $response = new WSResponse();
        $demos =    $this->boletosDao->getBoletosEscaneados($idEvento);
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

    public function getBoletosByIdListaInvitados($idListaInvitados){
        $response = new WSResponse();
        $data = $this->boletosDao->getBoletosByIdListaInvitados($idListaInvitados);
        if($data){
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($data);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }
        return $response->expose();
    }
    public function getListaInvitadosByIdLista($idListaInvitados){        
        $response = new WSResponse();
        $data = $this->boletosDao->getListaInvitadosByIdLista($idListaInvitados);
        if($data){
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($data);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }
        return $response->expose();
    }

    public function getKardex($idKardex){                
            $response = new WSResponse();
            $data = $this->boletosDao->getKardex($idKardex);
            if($data){
                $response->setSuccess(true);
                $response->setMessage("Registros obtenido correctamente");
                $response->setObject($data);
            } else {
                $response->setSuccess(false);
                $response->setMessage("No fue posible obtener los registros");
                $response->setObject(null);
            }
            return $response->expose();
    }
    public function updateKardex($params) {
            $response = new WSResponse();            
            $data = $this->boletosDao->updateKardex($params);
            if($data){
                $listaKardex = $this->boletosDao->getIdListaKardexByKardex($params["idkardexboletos"]);
                $listaInvitados = $this->boletosDao->getListaInvitadosByIdLista($listaKardex["idlistainvitados"]);
                $folioBoleto =$this-> boletosDao->getFolioBoletoByKardex($params["idkardexboletos"]);
             
                $key = $this->util->encrypt("{$folioBoleto["folioboletos"]}");
                           
                    
                $urlDescargaBoleto = URL_BASE."document/boletos/ticket.php?tk={$key}";

                $params["subject"] = "Descarga tu boleto";
                $params["from"] = "servicio@ticketapp.com";
                $params["fromName"] = "Ticket App";
                $params["toArray"] = $params["correopersona"];
                $params["body"] = "Por favor descarga tu boleto, gracias por tu preferencia <a href='{$urlDescargaBoleto}' _target='blank'>Descargar</a></b>";

                $this->util->sendMail($params);
                $response->setSuccess(true);
                $response->setMessage("Boleto enviado al invitado");
                $response->setObject($data);
            } else {
                $response->setSuccess(false);
                $response->setMessage("No fue posible enviar el boleto contacte al admionistrador");
                $response->setObject(null);
            }
            return $response->expose();
    }
    public function getCortesias($idTipoBoleto) {
        $response = new WSResponse();
        $data =  $this->boletosDao->getCortesias($idTipoBoleto);        
        
        $dataResponse = array();

       

        foreach ($data as $key => $value) {
            
            $tipo = "cortesias";
            
            $urlKey = $this->generateUrlKey($tipo, $value["idtipoboleto"], $value["idcortesia"]);

            $item = array(
              "fecha" => $value["fechaalta"],
              "nombrepersonacortesia" => $value["nombrepersonacortesia"],
              "correopersonacortesia" => $value["correopersonacortesia"],
              "telefonopersonacortesia" => $value["telefonopersonacortesia"],
              "numeroboletosdisponibles" => $value["numeroboletosdisponibles"],
              "motivocortesia" => $value["motivocortesia"],
              "motivocortesia" => $value["motivocortesia"],              
              "urlkey" => $urlKey
            );

            array_push($dataResponse, $item);
        }
     
        if($dataResponse && count($dataResponse) > 0){
            $response->setSuccess(true);
            $response->setMessage("Registro obtenido correctamente");
            $response->setObject($dataResponse);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener el registro");
            $response->setObject(null);
        }
        return $response->expose();
    }
    public function getBoletoInfo($folioBoleto) {
           return $this->boletosDao->getBoletoInfo($folioBoleto);
    }


    /**
     * return 1 todo OK
     * return 2 No esta pagado
     * return 3 Fallo Base de datos (UPDATE)
     * return 4 Si hay excepción
     */

    public function updateStatusKardexBoletosAfterPaying($idReferencia){

        try{
            //obtenemos el estatus del pago y se lo ponemos a los boletos y a la orden de compra 
            
            $cargo = $this->openPayDao->getPayStatus($idReferencia);
            
           
            if(strtolower($cargo->status) == strtolower("Completed")) {
               if($this->boletosDao->updateStatusKardexBoletosAfterPaying($idReferencia) ){

                   return 1;
               }else{
                   return 3;
               }
            }else{
              

                return 2;
            }


        }catch (Exception $e){
            return 4;

        }
    }
    public function getContactInfoToNotifyOnFullPayment($idReferenciaPago){
        return $this->boletosDao->getContactInfoToNotifyOnFullPayment($idReferenciaPago);
    }

    public function getIdTransaccionByOrdenCompra($idOrdenCompra){
        return $this->boletosDao->getIdTransaccionByOrdenCompra($idOrdenCompra);
    }

    public function cancelarOrdenCompra($idOrdenCompra){
        
        $responseDAO = FALSE;
        
        if($this->boletosDao->cancelarKardexBoletosByOrdenCompra($idOrdenCompra)){
            $responseDAO = $this->boletosDao->cancelarOrdenCompra($idOrdenCompra);
        }
        
        $response = new WSResponse();
        if($responseDAO){
            $response->setSuccess(true);
            $response->setMessage("Orden de compra cancelada");
            $response->setObject(null);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible cancelar la orden de compra");
            $response->setObject(null);
        }
        return $response->expose();  
    }
    public function esCortesiaOrListaInvitados($folioBoleto){
        return $this->boletosDao->esCortesiaOrListaInvitados($folioBoleto);
    }
    public function getLogosProductorByIdEvento($idEvento){
        return $this->boletosDao->getLogosProductorByIdEvento($idEvento);
    }
}