<?php

class OrdenCompraServiceImpl implements OrdenCompraService {

    private $ordenCompraDao;

    function __construct() {
        $this->ordenCompraDao = new OrdenCompraDaoImpl();
    }

    public function add($ordenCompra){        
        $response = new WSResponse();
        $insert =    $this->ordenCompraDao->add($ordenCompra);        
        if($insert){
            $response->setSuccess(true);
            $response->setMessage("Registro de ordencompra guardado correctamente");
            $response->setObject(null);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al realizar el registro de rden de compra");
            $response->setObject(null);
        }

        return $response->expose();
    }
    
    public function update($demo){
        $response = new WSResponse();
        $update =    $this->demoDao->update($demo);        
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
        $demo =    $this->demoDao->get($id);        
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
        $demos =    $this->demoDao->getAllData();
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
        $demo =    $this->demoDao->delete($id);        
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