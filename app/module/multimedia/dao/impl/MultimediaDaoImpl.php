<?php

class MultimediaDaoImpl extends Connection implements MultimediaDao {

    function __construct(){
    }
    public function add($multimedia){
        $query = "INSERT INTO multimedia(nombremultimedia,"
          ." urlmultimedia, extension, urlmultimediaget, idremote, fechaalta ) "
          ." VALUES ( '{$multimedia->getNombreMultimedia()}', "
          ." '{$multimedia->getUrlMultimedia()}', '{$multimedia->getExtensionMultimedia()}',"
          ." '{$multimedia->getUrlMultimediaGet()}', '{$multimedia->getIdRemote()}', now());";
        
        if ($this->executeQuery($query)) {            
            return $this->getLastInserId();
        } else {
            return FALSE;
        }
    }

   

    public function delete($idRemote){
        $query = "DELETE FROM multimedia WHERE idremote = '{$idRemote}'";
        
        if ($this->executeDelete($query)) {            
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get($id){
        $query = "SELECT * FROM multimedia WHERE idmultimedia = '{$id}'";
        
        return $this->getAll($query);           
    }
}
