<?php

class PatrocinadorDTO extends AuditoriaDTO{

    private $idpatrocinador;
    private $nombrepatrocinador;
    private $urlpatrocinador; 
    private $idusuario;
    private $idimagenpatrocinador;

    function setIdImagenPatrocinador($imagenpatrocinador){
        $this->imagenpatrocinador = $imagenpatrocinador;
    }

    function getIdImagenPatrocinador(){
        return $this->imagenpatrocinador;
    }
  

    function setIdPatrocinador($idpatrocinador){
        $this->idpatrocinador = $idpatrocinador;
    }

    function getIdPatrocinador(){
        return $this->idpatrocinador;
    }

    function setNombrePatrocinador($nombrepatrocinador){
        $this->nombrepatrocinador = $nombrepatrocinador;
    }

    function getNombrePatrocinador(){
        return $this->nombrepatrocinador;
    }

    function setUrlPatrocinador($urlpatrocinador){
        $this->urlpatrocinador = $urlpatrocinador;
    }

    function getUrlPatrocinador(){
        return $this->urlpatrocinador;
    }

    function setIdUsuario($idusuario){
        $this->idusuario = $idusuario;
    }

    function getIdUsuario(){
        return $this->idusuario;
    }
    
}