<?php

class CatalogsServiceImpl implements CatalogsService {

    private $catalogsDao;

    function __construct() {
        $this->catalogsDao = new CatalogsDaoImpl();
    }

    public function add($demo){        
        $response = new WSResponse();
        $insert =    $this->catalogsDao->add($demo);        
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
        $update =    $this->catalogsDao->update($demo);        
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

    public function get($catalog){        
        $response = new WSResponse();
        $catalogs =    $this->catalogsDao->get($catalog);        
        if($catalogs && count($catalogs) > 0){
            $response->setSuccess(true);
            $response->setMessage("Registros obtenidos correctamente");
            $response->setObject($catalogs);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function getAllData(){        
        $response = new WSResponse();
        $demos =    $this->catalogsDao->getAllData();
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
        $demo =    $this->catalogsDao->delete($id);        
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