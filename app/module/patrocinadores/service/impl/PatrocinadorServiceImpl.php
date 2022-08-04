<?php

class PatrocinadorServiceImpl implements PatrocinadorService {

    private $patrocinadorDao;

    function __construct() {
        $this->patrocinadorDao = new PatrocinadorDaoImpl();
    }

    public function add($patrocinador){        
        $response = new WSResponse();
        $insert =   $this->patrocinadorDao->add($patrocinador);        
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
    
    public function update($patrocinador){
        $response = new WSResponse();
        $update =    $this->patrocinadorDao->update($patrocinador);        
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
        $patrocinador =    $this->patrocinadorDao->get($id);        
        if($patrocinador && count($patrocinador) > 0){
            $response->setSuccess(true);
            $response->setMessage("Registro obtenido correctamente");
            $response->setObject($patrocinador[0]);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    
    public function getByEvent($id){        
        $response = new WSResponse();
        $patrocinadores =    $this->patrocinadorDao->getByEvent($id);
        if($patrocinadores){
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($patrocinadores);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }

     
    public function createEventoPatrocinador($idEvento, $idPatrocinador, $idUsuarioAlta){
        $response = new WSResponse();
        $insert =   $this->patrocinadorDao->createEventoPatrocinador($idEvento, $idPatrocinador, $idUsuarioAlta);        
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

    public function deleteEventoPatrocinador($idEventoPatrocinador){
        $response = new WSResponse();
        $delete =   $this->patrocinadorDao->deleteEventoPatrocinador($idEventoPatrocinador);        
        if($delete){
            $response->setSuccess(true);
            $response->setMessage("Registro eliminado correctamente");
            $response->setObject(null);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al eliminar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function getAllData(){        
        $response = new WSResponse();
        $patrocinadores =    $this->patrocinadorDao->getAllData();
        if($patrocinadores){
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($patrocinadores);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function getAllDataByUserId($id){        
        $response = new WSResponse();
        $patrocinadores =    $this->patrocinadorDao->getAllDataByUserId($id);
        if($patrocinadores){
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($patrocinadores);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function delete($id){
        $response = new WSResponse();
        
        $multimedia = $this->patrocinadorDao->getIdMultimediaByIdPatrocinador($id);
        
       
        $demo = $this->patrocinadorDao->delete($id); 

        if($demo){
            $response->setSuccess(true);
            $response->setMessage("Registro eliminado correctamente");
            $response->setObject(array("idremote" => $multimedia[0]["idremote"]));
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible eliminar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function getAllPatrocinadoresToAddByUserId($id, $idEvento){        
        $response = new WSResponse();
        $patrocinadores =    $this->patrocinadorDao->getAllPatrocinadoresToAddByUserId($id, $idEvento);
        if($patrocinadores){
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($patrocinadores);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    } 

    public function getIdMultimediaByIdPatrocinador($idPatrocinador){
        return $this->patrocinadorDao->getIdMultimediaByIdPatrocinador($idPatrocinador); 
    }

}