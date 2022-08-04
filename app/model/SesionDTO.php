<?php

class SesionDTO {

    private $idSesion;
    private $idSistema;
    private $idUsuario;
    private $horaCreacion;
    private $horaExpiracion;
    private $horaUltimaOperacion;
    private $fecha;

    function getFecha() {
        return $this->fecha;
    }

    function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    function getIdSesion() {
        return $this->idSesion;
    }

    function getIdSistema() {
        return $this->idSistema;
    }

    function getIdUsuario() {
        return $this->idUsuario;
    }

    function getHoraCreacion() {
        return $this->horaCreacion;
    }

    function getHoraExpiracion() {
        return $this->horaExpiracion;
    }

    function getHoraUltimaOperacion() {
        return $this->horaUltimaOperacion;
    }

    function setIdSesion($idSesion) {
        $this->idSesion = $idSesion;
    }

    function setIdSistema($idSistema) {
        $this->idSistema = $idSistema;
    }

    function setIdUsuario($idUsuario) {
        $this->idUsuario = $idUsuario;
    }

    function setHoraCreacion($horaCreacion) {
        $this->horaCreacion = $horaCreacion;
    }

    function setHoraExpiracion($horaExpiracion) {
        $this->horaExpiracion = $horaExpiracion;
    }

    function setHoraUltimaOperacion($horaUltimaOperacion) {
        $this->horaUltimaOperacion = $horaUltimaOperacion;
    }

    public function expose() {
        return get_object_vars($this);
    }

}
