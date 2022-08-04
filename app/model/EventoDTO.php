<?php
class EventoDTO extends AuditoriaDTO {
    private $idEvento;
    private $nombreEvento;
    private $urlEvento;
    private $descripcionEvento;
    private $videoPromocional;
    private $idUsuario;
    private $idCategoriaEvento;
    private $idTipoEvento;
    private $fechaInicio;
    private $fechaFin;
    private $horaInicio;
    private $horaFin;
    private $ocupacion;
    private $restricciones;
    private $horaFinBoleto;

    function getIdEvento() {
        return $this->idEvento;
    }
    function setIdEvento($idEvento) {
        $this->idEvento = $idEvento;
    }
    function getNombreEvento() {
        return $this->nombreEvento;
    }
    function setNombreEvento($nombreEvento) {
        $this->nombreEvento = $nombreEvento;
    }
    function getUrlEvento() {
        return $this->urlEvento;
    }
    function setUrlEvento($urlEvento) {
        $this->urlEvento = $urlEvento;
    }
    function getDescripcionEvento() {
        return $this->descripcionEvento;
    }
    function setDescripcionEvento($descripcionEvento) {
        $this->descripcionEvento = $descripcionEvento;
    }
    function getVideoPromocional() {
        return $this->videoPromocional;
    }
    function setVideoPromocional($videoPromocional) {
        $this->videoPromocional = $videoPromocional;
    }
    function getIdUsuario() {
        return $this->idUsuario;
    }
    function setIdUsuario($idUsuario) {
        $this->idUsuario = $idUsuario;
    }
    function getIdCategoriaEvento() {
        return $this->idCategoriaEvento;
    }
    function getIdTipoEvento() {
        return $this->idTipoEvento;
    }
    function setIdCategoriaEvento($idCategoriaEvento) {
        $this->idCategoriaEvento = $idCategoriaEvento;
    }
    function setIdTipoEvento($idTipoEvento) {
        $this->idTipoEvento = $idTipoEvento;
    }
    function getFechaInicio() {
        return $this->fechaInicio;
    }
    function setFechaInicio($fechaInicio) {
        $this->fechaInicio = $fechaInicio;
    }
    function getFechaFin() {
        return $this->fechaFin;
    }
    function setFechaFin($fechaFin) {
        $this->fechaFin = $fechaFin;
    }
    function getHoraInicio() {
        return $this->horaInicio;
    }
    function setHoraInicio($horaInicio) {
        $this->horaInicio = $horaInicio;
    }
    function getHoraFin() {
        return $this->horaFin;
    }
    function setHoraFin($horaFin) {
        $this->horaFin  = $horaFin;
    }

    function getHoraFinboleto() {
        return $this->horaFinBoleto;
    }
    function setHoraFinboleto($horaFinBoleto) {
        $this->horaFinBoleto  = $horaFinBoleto;
    }

    function getOcupacion() {
        return $this->ocupacion;
    }
    function setOcupacion($ocupacion) {
        $this->ocupacion  = $ocupacion;
    }
    function getRestricciones() {
        return $this->restricciones;
    }
    function setRestricciones($restricciones) {
        $this->restricciones  = $restricciones;
    }
    public function expose() {
        return get_object_vars($this);
    }
}
