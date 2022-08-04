<?php

class PatrocinadorDaoImpl extends Connection implements PatrocinadorDao {

    private $util;

    function __construct(){
        $this->util = new UtilImpl();
    }

    public function add($patrocinador){
        $query = "INSERT INTO patrocinadores( nombrepatrocinador, urlpatrocinador, idusuario, idusualta, estatus, fechaalta, idimagenpatrocinador) VALUES('{$patrocinador->getNombrePatrocinador()}', '{$patrocinador->getUrlPatrocinador()}', {$patrocinador->getIdUsuario()}, {$patrocinador->getIdUsuarioAlta()}, 1, now(), {$patrocinador->getIdImagenPatrocinador()} )";
       
        
        if ($this->executeQuery($query)) {            
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function update($patrocinador){

        $query = "UPDATE patrocinadores SET
        nombrepatrocinador ='{$patrocinador->getNombrePatrocinador()}',
        urlpatrocinador ='{$patrocinador->getUrlPatrocinador()}',
        idusuario ={$patrocinador->getIdUsuario()},     
        idusumodifica={$patrocinador->getIdUsuarioModifica()},
        idimagenpatrocinador={$patrocinador->getIdImagenPatrocinador()},
        fechamodifica=now()
        WHERE idpatrocinador = {$patrocinador->getIdPatrocinador()}";

     
        if ($this->executeQuery($query)) {            
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get($id){
        $query = "SELECT * FROM patrocinadores p LEFT JOIN multimedia m on m.idmultimedia = p.idimagenpatrocinador WHERE idpatrocinador = {$id}";        
        
        return $this->getAll($query);           
    }

    public function getAllData(){
        $query = "SELECT * FROM patrocinadores p LEFT JOIN multimedia m on m.idmultimedia = p.idimagenpatrocinador";        
        
        return $this->getAll($query);           
    }

    public function getAllDataByUserId($id){
        $query = "SELECT * FROM patrocinadores p LEFT JOIN multimedia m on m.idmultimedia = p.idimagenpatrocinador WHERE p.idusualta = {$id}";        
        
        return $this->getAll($query);           
    }

    public function createEventoPatrocinador($idEvent, $patrocinador, $idUsuarioAlta){
        $query = "INSERT INTO eventopatrocinadores (idevento, idpatrocinador, idusualta, fechaalta, estatus) VALUES({$idEvent}, {$patrocinador}, {$idUsuarioAlta}, now(), 1 )";
       
        return $this->executeQuery($query);           
    }

    public function deleteEventoPatrocinador($idEventoPatrocinador){
        $query = "DELETE FROM eventopatrocinadores where ideventopatrocinador = {$idEventoPatrocinador}";
       

        if ($this->executeDelete($query)) {            
            return TRUE;
        } else {
            return FALSE;
        }          
    }

    public function getByEvent($idEvent){
        $query = "SELECT e.ideventopatrocinador, p.idpatrocinador, p.nombrepatrocinador, p.urlpatrocinador from patrocinadores p
        join eventopatrocinadores e on e.idpatrocinador = p.idpatrocinador 
        where e.idevento = {$idEvent} ";        
       
        return $this->getAll($query);           
    }

    public function delete($id){
        $query = "DELETE FROM patrocinadores WHERE idpatrocinador = {$id}";
        
        if ($this->executeDelete($query)) {            
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function getAllPatrocinadoresToAddByUserId($id, $idEvento){
        $query = "SELECT ep.ideventopatrocinador, p.idpatrocinador, p.nombrepatrocinador, p.urlpatrocinador, m.urlmultimediaget FROM patrocinadores p
            LEFT JOIN multimedia m ON m.idmultimedia = p.idimagenpatrocinador
            LEFT JOIN eventopatrocinadores ep ON ep.idpatrocinador = p.idpatrocinador AND ep.idEvento = {$idEvento}
            WHERE p.idusualta = {$id}";        
        
        return $this->getAll($query);           
    }
    public function getIdMultimediaByIdPatrocinador($idPatrocinador){
       $query = "SELECT m.idremote, p.idpatrocinador FROM patrocinadores p
        INNER JOIN multimedia m ON m.idmultimedia = p.idimagenpatrocinador AND p.idpatrocinador = {$idPatrocinador}";
        return $this->getAll($query);   
    }
    public function getPatrocinadorIdByEvento($idEventoPatrocinador){
        $query = "SELECT p.idpatrocinador FROM patrocinadores p 
        INNER JOIN eventopatrocinadores ep ON ep.idpatrocinador = p.idpatrocinador AND ep.ideventopatrocinador = {$idEventoPatrocinador}";
       
       $response = $this->getAll($query);
        if(count($response) > 0){
            return $response[0]["idpatrocinador"];
        } else {
            return FALSE;
        }
    }
}
