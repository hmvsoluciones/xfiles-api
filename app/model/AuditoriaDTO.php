<?php

class AuditoriaDTO {

    private $idUsuarioAlta;
    private $fechaAlta;
    private $idUsuarioModifica;
    private $fechamodifica;
    private $estatus;
    
    function getIdUsuarioAlta() {
        return $this->idUsuarioAlta;
    }

    function getFechaAlta() {
        return $this->fechaAlta;
    }

    function getIdUsuarioModifica() {
        return $this->idUsuarioModifica;
    }

    function getFechamodifica() {
        return $this->fechamodifica;
    }

    function getEstatus() {
        return $this->estatus;
    }

    function setIdUsuarioAlta($idUsuarioAlta) {
        $this->idUsuarioAlta = $idUsuarioAlta;
    }

    function setFechaAlta($fechaAlta) {
        $this->fechaAlta = $fechaAlta;
    }

    function setIdUsuarioModifica($idUsuarioModifica) {
        $this->idUsuarioModifica = $idUsuarioModifica;
    }

    function setFechamodifica($fechamodifica) {
        $this->fechamodifica = $fechamodifica;
    }

    function setEstatus($estatus) {
        $this->estatus = $estatus;
    }
}