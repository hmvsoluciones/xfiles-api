<?php

class TicketsServiceImpl implements TicketsService {

    private $ticketsDao;

    function __construct() {
        $this->ticketsDao = new TicketsDaoImpl();
    }

    public function add($demo){        
        $response = new WSResponse();
        $insert =    $this->ticketsDao->add($demo);        
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
    
    public function update($demo){
        $response = new WSResponse();
        $update =    $this->ticketsDao->update($demo);        
        if($update){
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

    public function get($id){        
        $response = new WSResponse();
        $demo =    $this->ticketsDao->get($id);        
        if($demo && count($demo) > 0){
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

    public function getAllData(){        
        $response = new WSResponse();
        $demos =    $this->ticketsDao->getAllData();
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

    public function delete($id){
        $response = new WSResponse();
        $demo =    $this->ticketsDao->delete($id);        
        if($demo){
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

    

}