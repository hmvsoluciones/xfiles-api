<?php

class EventsServiceImpl implements EventsService
{
    private $eventsDao;
    private $multimediaDao;
    private $boletosDao;
    private $usersDao;
    private $util;

    public function __construct()
    {
        $this->eventsDao = new EventsDaoImpl();
        $this->multimediaDao = new MultimediaDaoImpl();
        $this->usersDao = new UsersDaoImpl();
        $this->boletosDao = new BoletosDaoImpl();
        $this->util = new UtilImpl();
    }

    public function add($demo)
    {
        $response = new WSResponse();
        $insertedId =    $this->eventsDao->add($demo);
        if ($insertedId) {
            $response->setSuccess(true);
            $response->setMessage("Registro guardado correctamente");
            $response->setObject($insertedId);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al realizar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }
    
    public function update($demo)
    {
        $response = new WSResponse();
        $update =    $this->eventsDao->update($demo);
        if ($update) {
            $response->setSuccess(true);
            $response->setMessage("Registro actualizado correctamente");
            $response->setObject(null);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al actualizar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function get($id)
    {
        $response = new WSResponse();
        $demo =    $this->eventsDao->get($id);
        if ($demo && count($demo) > 0) {
            $response->setSuccess(true);
            $response->setMessage("Registro obtenido correctamente");
            $response->setObject($demo[0]);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function getEventArray($id)
    {
        return   $this->eventsDao->get($id)[0];
    }

    public function getAllData()
    {
        $response = new WSResponse();
        $eventos =    $this->eventsDao->getAllData();
        $eventosResponse = array();

        $config_wildcard = parse_ini_file(__DIR__.'../../../../../config/config_wildcard.ini');

        foreach ($eventos as $key => $value) {
            /*$idEventoEncrypt = $this->util->encrypt($value["idevento"]);
            $idRPEncrypt = $this->util->encrypt("null");
            URL_BASE."event/?rp={$idRPEncrypt}&ev={$idEventoEncrypt}"
            */
            $urlEvento = ($config_wildcard["wildcard"] == 1)? $config_wildcard["protocol"]."".$value["urlevento"].".".$config_wildcard["domain"]:URL_BASE."event/?ev={$value["idevento"]}";

            $eventosItem = array(
                "ampusu" => $value["ampusu"],
                "appusu" => $value["appusu"],
                "categoriaevento" => $value["categoriaevento"],
                "correousu" => $value["correousu"],
                "descripcionevento" => $value["descripcionevento"],
                "fechaalta" => $value["fechaalta"],
                "fechafin" => $value["fechafin"],
                "fechainicio" => $value["fechainicio"],
                "horafin" => $value["horafin"],
                "horafinBoleto" => $value["horafinboletogratis"],
                "horainicio" => $value["horainicio"],
                "idcategoriaevento" => $value["idcategoriaevento"],
                "idevento" => $value["idevento"],
                "estatus" => $value["estatus"],
                "idtipoevento" => $value["idtipoevento"],
                "nombreevento" => $value["nombreevento"],
                "urlevento" => $value["urlevento"],
                "nombreusu" => $value["nombreusu"],
                "ocupacion" => $value["ocupacion"],
                "restricciones" => $value["restricciones"],
                "tipoevento" => $value["tipoevento"],
                "videopromocional" => $value["videopromocional"],
                "urlEvento" => $urlEvento
            );

            array_push($eventosResponse, $eventosItem);
        }
        if ($eventosResponse) {
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($eventosResponse);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }
    public function getAllEventosByIdUser($id)
    {
        $response = new WSResponse();
        $eventos =    $this->eventsDao->getAllEventosByIdUser($id);

        $eventosResponse = array();
        $config_wildcard = parse_ini_file(__DIR__.'../../../../../config/config_wildcard.ini');
        foreach ($eventos as $key => $value) {            
            /*$idEventoEncrypt = $this->util->encrypt($value["idevento"]);
            $idRPEncrypt = $this->util->encrypt("null");
            URL_BASE."event/?rp={$idRPEncrypt}&ev={$idEventoEncrypt}"
            */
            $urlEvento = ($config_wildcard["wildcard"] == 1)? $config_wildcard["protocol"]."".$value["urlevento"].".".$config_wildcard["domain"]:URL_BASE."event/?ev={$value["idevento"]}";

            $eventosItem = array(
                "ampusu" => $value["ampusu"],
                "appusu" => $value["appusu"],
                "categoriaevento" => $value["categoriaevento"],
                "correousu" => $value["correousu"],
                "descripcionevento" => $value["descripcionevento"],
                "fechaalta" => $value["fechaalta"],
                "fechafin" => $value["fechafin"],
                "fechainicio" => $value["fechainicio"],
                "horafin" => $value["horafin"],
                "horafinBoleto" => $value["horafinboletogratis"],
                "horainicio" => $value["horainicio"],
                "idcategoriaevento" => $value["idcategoriaevento"],
                "idevento" => $value["idevento"],
                "nombreevento" => $value["nombreevento"],
                "nombreusu" => $value["nombreusu"],
                "videopromocional" => $value["videopromocional"],
                "urlEvento" =>$urlEvento

            );

            array_push($eventosResponse, $eventosItem);
        }
        if ($eventosResponse) {
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($eventosResponse);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }
    ///////////////////////////////////////// 
    public function getAllEventosByIdUserRP($idUser)
    {
        $response = new WSResponse();
        $eventos =    $this->eventsDao->getAllEventosByIdUserRP($idUser);
        $eventosResponse = array();
        $eventosResponse = array();
        $config_wildcard = parse_ini_file(__DIR__.'../../../../../config/config_wildcard.ini');        
            
        foreach ($eventos as $key => $value) {
            $idRp = $this->util->encrypt($idUser);
            $idEvento = $this->util->encrypt($value["idevento"]);
            
            $urlEvento = ($config_wildcard["wildcard"] == 1)? $config_wildcard["protocol"]."".$value["urlevento"].".".$config_wildcard["domain"]:URL_BASE."event/?ev={$value["idevento"]}";
            $urlEvento = $urlEvento."?rp=".$idUser;

            $eventosItem = array(
            "idrelacionespublicas" => $value["idrelacionespublicas"],
            "idevento" => $value["idevento"],
            "rpcomplementourl"=> $value["rpcomplementourl"],
            "wcprotocol" => $config_wildcard["protocol"],
            "wcurlevento"=>$value["urlevento"],
            "wcdomain"=>$config_wildcard["domain"],
            "nombreevento" => $value["nombreevento"],
            "videopromocional" => $value["videopromocional"],
            "correousu" => $value["correousu"],
            "nombreusu" => $value["nombreusu"],
            "appusu" => $value["appusu"],
            "ampusu" => $value["ampusu"],
            "categoriaevento" => $value["categoriaevento"],
            "fechainicio" => $value["fechainicio"],
            "fechafin" => $value["fechafin"],
            "horainicio" => $value["horainicio"],
            "horafin" => $value["horafin"],
            "horafinBoleto" => $value["horafinboletogratis"],
            "urlrp" => $urlEvento
            //"urlrp" => URL_BASE."event/?rp={$idRp}&ev={$idEvento}"
        );

            array_push($eventosResponse, $eventosItem);
        }
        if ($eventosResponse) {
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($eventosResponse);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function delete($id)
    {
        $response = new WSResponse();
        $demo =    $this->eventsDao->delete($id);
        if ($demo) {
            $response->setSuccess(true);
            $response->setMessage("Registro eliminado correctamente");
            $response->setObject($id);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible eliminar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function addDescuentosEvento($descuento)
    {
        $response = new WSResponse();
        $insert =    $this->eventsDao->addDescuentosEvento($descuento);
        if ($insert) {
            $response->setSuccess(true);
            $response->setMessage("Registro guardado correctamente");
            $response->setObject(null);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al realizar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function addEstructurasEvento($estructura)
    {
        $response = new WSResponse();
        $insert =    $this->eventsDao->addEstructurasEvento($estructura);
        if ($insert) {
            $response->setSuccess(true);
            $response->setMessage("Registro guardado correctamente");
            $response->setObject(null);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al realizar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function addEstructurasEventoMultiple($estructura)
    {
        $arrayInserted = array();
        
        $multimediaItems = $estructura["multimediaItems"];

        foreach ($multimediaItems as $key => $value) {
            $objeto = new EventoEstructuraDTO();
            $objeto->setIdEvento($estructura['idEvento']);
            $objeto->setIdTipoElemento($estructura['idTipoElemento']);
            $objeto->setIdMultimedia($value['idMultimedia']);
            $objeto->setIdUsuarioAlta($estructura['idUsuarioAlta']);

            $insert = $this->eventsDao->addEstructurasEvento($objeto);

            array_push($arrayInserted, $insert);
        }

        $response = new WSResponse();
                
        if (count($arrayInserted) > 0) {
            $response->setSuccess(true);
            $response->setMessage("Registro guardado correctamente, ".count($arrayInserted)." archivo(s) cargados");
            $response->setObject(null);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al realizar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function addMensajeEvento($mensaje)
    {
        $response = new WSResponse();
        $insert =    $this->eventsDao->addMensajeEvento($mensaje);
        if ($insert) {
            $response->setSuccess(true);
            $response->setMessage("Registro guardado correctamente");
            $response->setObject(null);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al realizar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function addRedesSociales($redSocial)
    {
        $response = new WSResponse();
        $insert =    $this->eventsDao->addRedesSociales($redSocial);
        if ($insert) {
            $response->setSuccess(true);
            $response->setMessage("Registro guardado correctamente");
            $response->setObject(null);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al realizar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }
    public function addRedesSocialesAll($redesSociales)
    {
        $response = new WSResponse();

        $idEvento = $redesSociales["idEvento"];
        $idUsuario = $redesSociales["idUsuarioAlta"];
        
        try {
            foreach ($redesSociales["redes"] as $key => $value) {
                $redSocial = new RedesSocialesEventoDTO();

                $redSocial->setUrlRedSocial($value["url"]);
                $redSocial->setIdEvento($idEvento);
                $redSocial->setIdTipoRedSocial($value["id"]);
                $redSocial->setIdUsuarioAlta($idUsuario);
                $redSocial->setIdUsuarioModifica($idUsuario);
               
                $tipoRedSocial = $value["id"];
               
                $existsRed = $this->eventsDao->exitsRedSocial($idEvento, $tipoRedSocial);
          

                if (count($existsRed) > 0) {
                    $this->eventsDao->updateRedSocialUrl($redSocial);
                } elseif (!empty($value["url"])) {
                    $this->eventsDao->addRedesSociales($redSocial);
                }
            }

            $response->setSuccess(true);
            $response->setMessage("Redes sociales guardadas correctamente");
            $response->setObject(null);
        } catch (Exception $e) {
            $response->setSuccess(true);
            $response->setMessage("Ocurrio un error al registrar las redes sociales: {$e->getMessage()}");
            $response->setObject(null);
        }
        return $response->expose();
    }
    public function addListaInvitados($ListaInvitados)
    {
        $response = new WSResponse();
        $insert =    $this->eventsDao->addListaInvitados($ListaInvitados);
        if ($insert) {
            $response->setSuccess(true);
            $response->setMessage("Registro guardado correctamente");
            $response->setObject(null);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al realizar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function addUbicacionEvento($ubicacion)
    {
        $response = new WSResponse();
        $insert =    $this->eventsDao->addUbicacionEvento($ubicacion);
        if ($insert) {
            $insertado = $this->eventsDao->getAllUbicacionesEventoByEvento($ubicacion->getIdEvento());
            $response->setSuccess(true);
            $response->setMessage("Registro guardado correctamente");
            $response->setObject($insertado[0]);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al realizar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function deleteDescuentosEvento($idDescuento)
    {
        $response = new WSResponse();
        $demo =    $this->eventsDao->deleteDescuentosEvento($idDescuento);
        if ($demo) {
            $response->setSuccess(true);
            $response->setMessage("Registro eliminado correctamente");
            $response->setObject($idDescuento);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible eliminar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function deleteEstructurasEvento($idEstructura)
    {
        $response = new WSResponse();
        
        $estructura = $this->eventsDao->getEstructurasEvento($idEstructura);
        $multimedia = $this->multimediaDao->get($estructura[0]['idmultimedia']);
                
        $demo =    $this->eventsDao->deleteEstructurasEvento($idEstructura);

        if ($demo) {
            $response->setSuccess(true);
            $response->setMessage("Registro eliminado correctamente");
            $response->setObject(array("estructura" => $estructura[0], "multimedia" => $multimedia[0]));
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible eliminar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function deleteMensajesEvento($idMensajeEvento)
    {
        $response = new WSResponse();
        $demo =    $this->eventsDao->deleteMensajesEvento($idMensajeEvento);
        if ($demo) {
            $response->setSuccess(true);
            $response->setMessage("Registro eliminado correctamente");
            $response->setObject($idMensajeEvento);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible eliminar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function deleteRedesSociales($idRedSocial)
    {
        $response = new WSResponse();
        $demo =    $this->eventsDao->deleteRedesSociales($idRedSocial);
        if ($demo) {
            $response->setSuccess(true);
            $response->setMessage("Registro eliminado correctamente");
            $response->setObject($idRedSocial);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible eliminar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function deleteListaInvitados($idListaInvitados)
    {
        $response = new WSResponse();
        $demo =    $this->eventsDao->deleteListaInvitados($idListaInvitados);
        if ($demo) {
            $response->setSuccess(true);
            $response->setMessage("Registro eliminado correctamente");
            $response->setObject($idListaInvitados);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible eliminar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function deleteUbicacionEvento($idUbicacion)
    {
        $response = new WSResponse();
        $demo =    $this->eventsDao->deleteUbicacionEvento($idUbicacion);
        if ($demo) {
            $response->setSuccess(true);
            $response->setMessage("Registro eliminado correctamente");
            $response->setObject($idUbicacion);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible eliminar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function getAllDescuentosEventoByEvento($idEvento)
    {
        $response = new WSResponse();
        $demos =    $this->eventsDao->getAllDescuentosEventoByEvento($idEvento);
        if ($demos) {
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


    public function getBoletosVendidos()
    {
        $response = new WSResponse();
        echo 'setvice';
        exit();
        $demos =    $this->eventsDao->getBoletosVendidos();
        if ($demos) {
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($demos);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros x");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function getAllEstructurasEventoByEvento($idEvento)
    {
        $response = new WSResponse();
        $demos =    $this->eventsDao->getAllEstructurasEventoByEvento($idEvento);
        if ($demos) {
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

    public function getAllMensajesEventoByEvento($idEvento)
    {
        $response = new WSResponse();
        $demos =    $this->eventsDao->getAllMensajesEventoByEvento($idEvento);
        if ($demos) {
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

    public function getAllRedesSocialesByEvento($idEvento)
    {
        $response = new WSResponse();
        $demos =    $this->eventsDao->getAllRedesSocialesByEvento($idEvento);
        if ($demos) {
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


    

    

    public function getDescuentosEvento($idDescuento)
    {
        $response = new WSResponse();
        $demos =    $this->eventsDao->getDescuentosEvento($idDescuento);
        if ($demos && count($demos) > 0) {
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($demos[0]);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function getEstructurasEvento($idEstructura)
    {
        $response = new WSResponse();
        $demos =    $this->eventsDao->getEstructurasEvento($idEstructura);
        if ($demos && count($demos) > 0) {
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($demos[0]);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function getMensajeEvento($idMensajeEvento)
    {
        $response = new WSResponse();
        $demos =    $this->eventsDao->getMensajeEvento($idMensajeEvento);
        if ($demos && count($demos) > 0) {
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($demos[0]);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function getRedesSociales($idRedSocial)
    {
        $response = new WSResponse();
        $demos =    $this->eventsDao->getRedesSociales($idRedSocial);
        if ($demos && count($demos) > 0) {
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($demos[0]);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function getListaInvitados($idListaInvitados)
    {
        $response = new WSResponse();
        $demos =    $this->eventsDao->getListaInvitados($idListaInvitados);
        if ($demos && count($demos) > 0) {
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($demos[0]);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function getUbicacionEvento($idUbicacion)
    {
        $response = new WSResponse();
        $demos =    $this->eventsDao->getUbicacionEvento($idUbicacion);
        if ($demos && count($demos) > 0) {
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($demos[0]);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function getAllUbicacionesEventoByEvento($idEvento)
    {
        $response = new WSResponse();
        $demos =    $this->eventsDao->getAllUbicacionesEventoByEvento($idEvento);
        if ($demos) {
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($demos[0]);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function updateDescuentosEvento($descuento)
    {
        $response = new WSResponse();
        $update =    $this->eventsDao->updateDescuentosEvento($descuento);
        if ($update) {
            $response->setSuccess(true);
            $response->setMessage("Registro actualizado correctamente");
            $response->setObject(null);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al actualizar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function updateEstructurasEvento($estructura)
    {
        $response = new WSResponse();
        $update =    $this->eventsDao->updateEstructurasEvento($estructura);
        if ($update) {
            $response->setSuccess(true);
            $response->setMessage("Registro actualizado correctamente");
            $response->setObject(null);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al actualizar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function updateMensajeEvento($mensaje)
    {
        $response = new WSResponse();
        $update =    $this->eventsDao->updateMensajeEvento($mensaje);
        if ($update) {
            $response->setSuccess(true);
            $response->setMessage("Registro actualizado correctamente");
            $response->setObject(null);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al actualizar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function updateRedesSociales($redSocial)
    {
        $response = new WSResponse();
        $update =    $this->eventsDao->updateRedesSociales($redSocial);
        if ($update) {
            $response->setSuccess(true);
            $response->setMessage("Registro actualizado correctamente");
            $response->setObject(null);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al actualizar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function updateListaInvitados($ListaInvidatos)
    {
        $response = new WSResponse();
        $update =    $this->eventsDao->updateRedesSociales($ListaInvidatos);
        if ($update) {
            $response->setSuccess(true);
            $response->setMessage("Registro actualizado correctamente");
            $response->setObject(null);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al actualizar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function updateUbicacionEvento($ubicacion)
    {
        $response = new WSResponse();
        $update =    $this->eventsDao->updateUbicacionEvento($ubicacion);
        if ($update) {
            $response->setSuccess(true);
            $response->setMessage("Registro actualizado correctamente");
            $response->setObject(null);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al actualizar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function getEventSites($filtros, $nombreEvento, $fechaEvento, $idEvento, $sesionUser)
    {
        $response = new WSResponse();
        $events = $this->eventsDao->getAllSiteEvents($filtros, $nombreEvento, $fechaEvento, $idEvento, $sesionUser);
                        
        $response->setSuccess(true);
        $response->setMessage("Registros obtenido correctamente");
        $response->setObject($events);
    
        return $response->expose();
    }

    public function getEventInfo($event)
    {
        $estructura = $this->eventsDao->getEventEstructura($event);
        $evento = $this->eventsDao->get($event);
        $patrocinadores = $this->eventsDao->getEventPatrociandores($event);
        $redesSociales = $this->eventsDao->getAllRedesSocialesByEvento($event);
        $ubicacion = $this->eventsDao->getAllUbicacionesEventoByEvento($event);
        $tickets =    $this->eventsDao->getAllActiveTipoBoletoByEvento($event);

        //Obtenemos comision  del usuario productor
        $usuarioProductor = $this->usersDao->get($evento[0]['idusuario']);        
        $comisionUsuario = (int)$usuarioProductor[0]['porcentajecomision'];
        $comisionServicio = (int) COMISION_FIJA_SERVICIO;

        $logoProductor = $this->usersDao->getLogoProductorMultimedia($evento[0]['idusuario']);

        foreach ($tickets as &$tipoBoleto) {
            $maximoDeBoletos=  $this->boletosDao->getMaximoBoletos($tipoBoleto['idtipoboleto']) ;
 
            $boletosNoDisponibles = $this->boletosDao->getBoletosInKardexByTipoBoleto($tipoBoleto['idtipoboleto']);
         
            
            $tipoBoleto['boletosDisponibles'] = $maximoDeBoletos - $boletosNoDisponibles;
        }
        
        $response = new WSResponse();
        $responseJson = array(
            "estructura" => $estructura,
            "evento" => $evento[0],
            "patrocinadores" => $patrocinadores,
            "redesSociales" => $redesSociales,
            "ubicacion" => $ubicacion[0],
            "tickets"=>$tickets,
            "comisionUsuario" => $comisionUsuario,
            "comisionFija" => $comisionServicio,
            "logoProductor" => $logoProductor
        );
                        
        $response->setSuccess(true);
        $response->setMessage("Registros obtenidos correctamente");
        $response->setObject($responseJson);
    
        return $response->expose();
    }

    


    public function saveTipoBoletoEvento($tipoBoleto)
    {
        $response = new WSResponse();
        try {
            if (!empty($tipoBoleto->getIdTipoBoleto())) {
                $save = $this->eventsDao->updateTipoBoletoEvento($tipoBoleto);
            } else {
                $ocupacion = $this->boletosDao->getCupoEvento($tipoBoleto->getidEvento());
                //echo '-';
                $boletosCreadosEvento=$this->boletosDao->getBoletosCreadosEvento($tipoBoleto->getidEvento());
                //echo'-';
                $validaboletos= $boletosCreadosEvento + $tipoBoleto->getCantidadBoletos();
                //exit;
                if ($ocupacion < $validaboletos) {
                    throw new Exception('Se estaria excediendo la cantidad de boletos para este tipo');
                }

                $save = $this->eventsDao->addTipoBoletoEvento($tipoBoleto);
            }
            
            if ($save) {
                $response->setSuccess(true);
                $response->setMessage("Registro guardado correctamente");
                $response->setObject(null);
            } else {
                $response->setSuccess(false);
                $response->setMessage("Ocurrio un error al realizar el registro");
                $response->setObject(null);
            }
        } catch (Exception $e) {

            //$this->boletosDao->rollBackTransaction();

            $response->setSuccess(false);
            $response->setMessage("No fue posible crear el tipo de boleto: ".$e->getMessage());
            $response->setObject(null);
        }
        return $response->expose();
    }

    public function getAllTipoBoletoByEvento($idEvento)
    {
        $response = new WSResponse();
        $tiposBoleto =    $this->eventsDao->getAllTipoBoletoByEvento($idEvento);
        if ($tiposBoleto) {
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($tiposBoleto);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }
    public function getTipoBoletoById($idTipoBoleto)
    {
        $response = new WSResponse();
        $tipoBoleto =    $this->eventsDao->getTipoBoletoById($idTipoBoleto);
        if ($tipoBoleto && count($tipoBoleto) > 0) {
            $response->setSuccess(true);
            $response->setMessage("Registro obtenido correctamente");
            $response->setObject($tipoBoleto[0]);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }
    public function deleteTipoBoletoById($id)
    {
        $response = new WSResponse();
        $demo =    $this->eventsDao->deleteTipoBoletoById($id);
        if ($demo) {
            $response->setSuccess(true);
            $response->setMessage("Registro eliminado correctamente");
            $response->setObject($id);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible eliminar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }
    public function addUsuarioAppEvento($usuarioApp)
    {
        $response = new WSResponse();

        $usuarioApp->setNip(rand(1000, 9999));
        
        if (!$this->eventsDao->existsUsuarioAppEvento($usuarioApp->getIdEvento(), $usuarioApp->getIdUsuario())) {
            $save = $this->eventsDao->addUsuarioAppEvento($usuarioApp);
            if ($save) {
                $response->setSuccess(true);
                $response->setMessage("Usuario agregado correctamente al evento");
                $response->setObject(null);

                $evento = $this->eventsDao->get($usuarioApp->getIdEvento());
                if (count($evento) > 0) {
                    $user = $this->usersDao->get($usuarioApp->getIdUsuario());

                    $params["subject"] = "Registro ticketapp";
                    $params["from"] = "servicio@ticketapp.com";
                    $params["fromName"] = "Ticket App";
                    $params["toArray"] = $user[0]['correousu'];
                    $params["body"] = "Usted ha sido asignado al evento: <b>{$evento[0]['nombreevento']}</b> con el rol <b>{$user[0]['tipoUsuDesc']}</b> con el NIP:<b>{$usuarioApp->getNip()}</b>";
                    $this->util->sendMail($params);
                }
            } else {
                $response->setSuccess(false);
                $response->setMessage("Ocurrio un error al realizar el registro");
                $response->setObject(null);
            }
        } else {
            $response->setSuccess(true);
            $response->setMessage("El usuario ya se encuentra agregado al evento");
            $response->setObject(null);
        }

        return $response->expose();
    }
    public function getAllUsuariosAppByEvento($idEvento)
    {
        $response = new WSResponse();
        $usuariosEvento =    $this->eventsDao->getAllUsuariosAppByEvento($idEvento);
        if ($usuariosEvento) {
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($usuariosEvento);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }
    public function deleteUsuarioAppById($id)
    {
        $response = new WSResponse();
        $demo =    $this->eventsDao->deleteUsuarioAppById($id);
        if ($demo) {
            $response->setSuccess(true);
            $response->setMessage("Registro eliminado correctamente");
            $response->setObject($id);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible eliminar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function getEventTicket($event)
    {
        $estructura = $this->eventsDao->getEventEstructura($event);
        $redesSociales = $this->eventsDao->getAllRedesSocialesByEvento($event);
        $evento = $this->eventsDao->get($event);
        $tickets = $this->eventsDao->getAllTipoBoletoByEvento($event);

        //agregamos comision  del usuario productor
        $usuarioProductor = $this->usersDao->get($evento[0]['idusuario']);

        $comisionUsuario = (int)$usuarioProductor[0]['porcentajecomision'];
        $comisionServicio = (int) COMISION_FIJA_SERVICIO;
     

        foreach ($tickets as &$tipoBoleto) {
            $maximoDeBoletos=  $this->boletosDao->getMaximoBoletos($tipoBoleto['idtipoboleto']) ;
 
            $boletosNoDisponibles = $this->boletosDao->getBoletosInKardexByTipoBoleto($tipoBoleto['idtipoboleto']);
         
            
            $tipoBoleto['boletosDisponibles'] = $maximoDeBoletos - $boletosNoDisponibles;
        }
 
 
 
        
        $response = new WSResponse();
        $responseJson = array(
            "evento" => $evento[0],
            "tickets" => $tickets,
            "estructura" => $estructura,
            "redesSociales" => $redesSociales,
            "comisionUsuario" => $comisionUsuario,
            "comisionFija" => $comisionServicio
        );
                        
        $response->setSuccess(true);
        $response->setMessage("Registros obtenidos correctamente");
        $response->setObject($responseJson);
    
        return $response->expose();
    }
/////////////////////////////////////////////////////7

///////////////////////////////////////////////////////
    public function getAllUsuariosRelPublicByEvento($idEvento)
    {
        $response = new WSResponse();
        $usuariosEvento =    $this->eventsDao->getAllUsuariosRelPublicByEvento($idEvento);
        if ($usuariosEvento) {
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($usuariosEvento);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function addUsuarioRPEvento($usuarioApp)
    {
        $response = new WSResponse();

        //$usuarioApp->setNip(rand(1000,9999));
        
        if (!$this->eventsDao->existsUsuarioRPEvento($usuarioApp->getIdEvento(), $usuarioApp->getIdUsuario())) {
            $save = $this->eventsDao->addUsuarioRPEvento($usuarioApp);
            if ($save) {
                $response->setSuccess(true);
                $response->setMessage("Usuario agregado correctamente al evento");
                $response->setObject(null);

                $evento = $this->eventsDao->get($usuarioApp->getIdEvento());
                if (count($evento) > 0) {
                    $user = $this->usersDao->get($usuarioApp->getIdUsuario());

                    $params["subject"] = "Registro ticketapp";
                    $params["from"] = "servicio@ticketapp.com";
                    $params["fromName"] = "Ticket App";
                    $params["toArray"] = $user[0]['correousu'];
                    $params["body"] = "Usted ha sido asignado al evento: <b>{$evento[0]['nombreevento']}</b> con el rol <b>{$user[0]['tipoUsuDesc']}</b>";
                    $this->util->sendMail($params);
                }
            } else {
                $response->setSuccess(false);
                $response->setMessage("Ocurrio un error al realizar el registro");
                $response->setObject(null);
            }
        } else {
            $response->setSuccess(true);
            $response->setMessage("El usuario ya se encuentra agregado al evento");
            $response->setObject(null);
        }

        return $response->expose();
    }
    public function deleteUsuarioRPById($id)
    {
        $response = new WSResponse();
        $demo =    $this->eventsDao->deleteUsuarioRPById($id);
        if ($demo) {
            $response->setSuccess(true);
            $response->setMessage("Registro eliminado correctamente");
            $response->setObject($id);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible eliminar el registro x");
            $response->setObject(null);
        }

        return $response->expose();
    }
    public function updateStatus($idEvent, $newStatus)
    {
        $response = new WSResponse();
        $update = $this->eventsDao->updateStatus($idEvent, $newStatus);
        if ($update) {
            $response->setSuccess(true);
            $response->setMessage("Estatus modifcado correctamente");
            $response->setObject(null);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al actualizar el estatus");
            $response->setObject(null);
        }

        return $response->expose();
    }
    public function publicarEvento($idEvent)
    {
        $response = new WSResponse();
        $update = $this->eventsDao->publicarEvento($idEvent);
        if ($update) {
            $response->setSuccess(true);
            $response->setMessage("Evento publicado correctamente");
            $response->setObject(null);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al publicar el evento");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function getReporteVentasPorEvento($idEvento)
    {
        $response = new WSResponse();
        $reporte =    $this->eventsDao->getReporteVentasPorEvento($idEvento);
        
        if ($reporte) {
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($reporte);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function getReporteAbonosPorEvento($idEvento)
    {
        $response = new WSResponse();
        $reporte =    $this->eventsDao->getReporteAbonoPorEvento($idEvento);
        
        if ($reporte) {
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($reporte);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }
// Reporte de puntos de venta 
    public function getReportePVPorEvento($idEvento)
    {
        $response = new WSResponse();
        $reporte =    $this->eventsDao->getReportePVPorEvento($idEvento);
        
        if ($reporte) {
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($reporte);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }
    // Reporte de accesos al evento
    public function getReporteAEPorEvento($idEvento)
    {
        $response = new WSResponse();
        $reporte =    $this->eventsDao->getReporteAEPorEvento($idEvento);
        
        if ($reporte) {
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($reporte);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }
//reporte de RP
    public function getReporterpventasPorEvento($idEvento)
    {
        $response = new WSResponse();
        $reporte =    $this->eventsDao->getReporteventasrpPorEvento($idEvento);
        
        if ($reporte) {
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($reporte);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }


    public function findAddUrlEvento($url){
        $response = new WSResponse();
        $event =    $this->eventsDao->findAddUrlEvento($url);        
        if($event && count($event) > 0){
            $response->setSuccess(true);
            $response->setMessage("Registro obtenido correctamente");
            $response->setObject($event[0]);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener el registro");
            $response->setObject(null);
        }
        return $response->expose();
    }

    public function findUpdateUrlEvento($url, $idEvento){
        $response = new WSResponse();
        $event =    $this->eventsDao->findUpdateUrlEvento($url, $idEvento);        
        if($event && count($event) > 0){
            $response->setSuccess(true);
            $response->setMessage("Registro obtenido correctamente");
            $response->setObject($event[0]);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener el registro");
            $response->setObject(null);
        }
        return $response->expose();
    }
    public function getIdEventoByUrlEvento($urlEvento){
        return $this->eventsDao->getIdEventoByUrlEvento($urlEvento);  
    }

    
    public function getReporteTableroPorEvento($idEvento)
    {
        $response = new WSResponse();
        $reporte =    $this->eventsDao->getReporteTableroPorEvento($idEvento);
        
        if ($reporte) {
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($reporte);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }

    //Detalle de boleto

    public function getDetalleBoleto($idEvento)
    {
        $response = new WSResponse();
        $reporte =    $this->eventsDao->getDetalleBoleto($idEvento);
        
        if ($reporte) {
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($reporte);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }


    //Detalle de orden de RP

    public function getDetalleOrdenRP($idorden)
    {
        $response = new WSResponse();
        $reporte =    $this->eventsDao->getDetalleOrdenRP($idorden);
        
        if ($reporte) {
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($reporte);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }

    //Detalle de ventas - Reporte integral
    //public function getDetalleVentas($eventos,$fechainicio,$fechafin)
    public function getDetalleVentas($eventos,$fechainicio,$fechafin)
    {
        $response = new WSResponse();
        $reporte =    $this->eventsDao-> getDetalleVentas($eventos,$fechainicio,$fechafin);
        //$reporte =    $this->eventsDao-> getDetalleVentas($eventos,$fechainicio,$fechafin);
        
        if ($reporte) {
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($reporte);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function findAddUrlEventoRP($url){
        $response = new WSResponse();
        $event =    $this->eventsDao->findAddUrlEventoRP($url);        
        if($event && count($event) > 0){
            $response->setSuccess(true);
            $response->setMessage("Registro obtenido correctamente");
            $response->setObject($event[0]);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener el registro");
            $response->setObject(null);
        }
        return $response->expose();
    }
    public function saveComplementoURLRP($idRelacionesPublicas, $complementourlrp){
        $response = new WSResponse();
        $save = $this->eventsDao->saveComplementoURLRP($idRelacionesPublicas, $complementourlrp);
        if ($save) {
            $response->setSuccess(true);
            $response->setMessage("URL personalizada agregada correctamente");
            $response->setObject(null);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible crear la URL personalizada, contacte al administrador");
            $response->setObject(null);
        }
        return $response->expose();
    }
    public function changeStatusTipoBoleto($idTipoBoleto, $newStatus)
    {
        $response = new WSResponse();
        $demo =    $this->eventsDao->changeStatusTipoBoleto($idTipoBoleto, $newStatus);
        if ($demo) {
            $response->setSuccess(true);
            $response->setMessage("Registro actualizado correctamente");
            $response->setObject(null);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible actualizar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }
}
