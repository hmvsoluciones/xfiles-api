<?php

class EventActionsServiceImpl implements EventActionsService {

    private $demoDao;

    function __construct() {
        $this->eventActionsDao = new EventActionsDaoImpl();
    }

    public function like($idEvent, $idUser){
        $response = new WSResponse();
        $insert =    $this->eventActionsDao->like($idEvent, $idUser);        
        if($insert){
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
    public function unlike($idEvent, $idUser){
        $response = new WSResponse();
        $insert =    $this->eventActionsDao->unlike($idEvent, $idUser);        
        if($insert){
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


}