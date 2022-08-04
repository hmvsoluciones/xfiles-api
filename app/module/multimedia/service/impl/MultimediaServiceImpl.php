<?php

class MultimediaServiceImpl implements MultimediaService {

    private $demoDao;

    function __construct() {
        $this->multimediaDao = new MultimediaDaoImpl();
    }

    public function add($multimedia){        
        $response = new WSResponse();
        $insert =    $this->multimediaDao->add($multimedia);        
        if($insert){
            $multimedia->setIdMultimedia($insert);
            $response->setSuccess(true);
            $response->setMessage("Archivo registrado correctamente");
            $response->setObject($multimedia->expose());
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al registrar el archivo");
            $response->setObject(null);
        }

        return $response->expose();
    }
    public function addMultiple($multimedia){                
        $insert =    $this->multimediaDao->add($multimedia);        
        if($insert){
            $multimedia->setIdMultimedia($insert);
            return $multimedia->expose();
        } else 
            return NULL;
    }
    
    
    public function delete($idRemote){
        $response = new WSResponse();
        $demo =    $this->multimediaDao->delete($idRemote);        
        if($demo){
            $response->setSuccess(true);
            $response->setMessage("Registro eliminado correctamente");
            $response->setObject($idRemote);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible eliminar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function get($id){        
        $response = new WSResponse();
        $demo =    $this->multimediaDao->get($id);        
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

}