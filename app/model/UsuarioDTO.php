<?php

class UsuarioDTO extends AuditoriaDTO {

    private $idUsuario;
    private $correoUsu;
    private $contraseniaUsu;
    private $nombreUsu;
    private $appUsu;
    private $apmUsu;
    private $idTipoUsu;
    private $telefonoUsu;
    private $direccionUsu;
    private $porcentajeComision;
    private $estado;   
    function getEstado() {
        return $this->estado;
    }
    function getPorcentajeComision() {
        return $this->porcentajeComision;
    }
    function getIdUsuario() {
        return $this->idUsuario;
    }

    function getCorreoUsu() {
        return $this->correoUsu;
    }

    function getContraseniaUsu() {
        return $this->contraseniaUsu;
    }

    function getNombreUsu() {
        return $this->nombreUsu;
    }

    function getAppsusu() {
        return $this->appUsu;
    }

    function getApmsusu() {
        return $this->apmUsu;
    }

    function getIdTipoUsu() {
        return $this->idTipoUsu;
    }
    function gettelefonoUsu() {
        return $this->telefonoUsu;
    }
    function getdireccionUsu() {
        return $this->direccionUsu;
    }    

    function setEstado($estado) {
        $this->estado = $estado;
    }

    function setIdUsuario($idUsuario) {
        $this->idUsuario = $idUsuario;
    }

    function setPorcentajeComision($porcentajeComision) {
        $this->porcentajeComision = $porcentajeComision;
    }

    function setCorreoUsu($correoUsu) {
        $this->correoUsu = $correoUsu;
    }

    function setContraseniaUsu($contraseniaUsu) {
        $this->contraseniaUsu = $contraseniaUsu;
    }

    function setNombreUsu($nombreUsu) {
        $this->nombreUsu = $nombreUsu;
    }

    function setAppUsu($appUsu) {
        $this->appUsu = $appUsu;
    }

    function setApmUsu($apmUsu) {
        $this->apmUsu = $apmUsu;
    }

    function setIdTipoUsu($idTipoUsu) {
        $this->idTipoUsu = $idTipoUsu;
    }
    function setTelefonoUsu($telefonoUsu){
        $this -> telefonoUsu = $telefonoUsu;
    }
    function setDireccionUsu($direccionUsu){
        $this -> direccionUsu = $direccionUsu;
    }

    public function expose() {
        return get_object_vars($this);
    }
}
