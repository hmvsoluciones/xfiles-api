<?php
class EventoSiteDTO extends AuditoriaDTO {
    private $idEvento;
    private $nombreEvento;
    private $descripcionEvento;
    private $idUsuario;
    private $idCategoriaEvento;
    private $fechaInicio;
    private $fechaFin;
    private $horaInicio;
    private $horaFin;

    private $eventoestructuras;
    private $mensajesevento;
    private $redessocialesevento;
    private $ubicacionevento;
    private $descuentosevento;

    function getIdEvento() {
        return $this->idEvento;
    }

    function getNombreEvento() {
        return $this->nombreEvento;
    }

    function getDescripcionEvento() {
        return $this->descripcionEvento;
    }

    function getIdUsuario() {
        return $this->idUsuario;
    }

    function getIdCategoriaEvento() {
        return $this->idCategoriaEvento;
    }

    function getFechaInicio() {
        return $this->fechaInicio;
    }

    function getFechaFin() {
        return $this->fechaFin;
    }

    function getHoraInicio() {
        return $this->horaInicio;
    }

    function getHoraFin() {
        return $this->horaFin;
    }

    function getEventoestructuras() {
        return $this->eventoestructuras;
    }

    function getMensajesevento() {
        return $this->mensajesevento;
    }

    function getRedessocialesevento() {
        return $this->redessocialesevento;
    }

    function getUbicacionevento() {
        return $this->ubicacionevento;
    }

    function getDescuentosevento() {
        return $this->descuentosevento;
    }

    function setIdEvento($idEvento) {
        $this->idEvento = $idEvento;
    }

    function setNombreEvento($nombreEvento) {
        $this->nombreEvento = $nombreEvento;
    }

    function setDescripcionEvento($descripcionEvento) {
        $this->descripcionEvento = $descripcionEvento;
    }

    function setIdUsuario($idUsuario) {
        $this->idUsuario = $idUsuario;
    }

    function setIdCategoriaEvento($idCategoriaEvento) {
        $this->idCategoriaEvento = $idCategoriaEvento;
    }

    function setFechaInicio($fechaInicio) {
        $this->fechaInicio = $fechaInicio;
    }

    function setFechaFin($fechaFin) {
        $this->fechaFin = $fechaFin;
    }

    function setHoraInicio($horaInicio) {
        $this->horaInicio = $horaInicio;
    }

    function setHoraFin($horaFin) {
        $this->horaFin = $horaFin;
    }

    function setEventoestructuras($eventoestructuras) {
        $this->eventoestructuras = $eventoestructuras;
    }

    function setMensajesevento($mensajesevento) {
        $this->mensajesevento = $mensajesevento;
    }

    function setRedessocialesevento($redessocialesevento) {
        $this->redessocialesevento = $redessocialesevento;
    }

    function setUbicacionevento($ubicacionevento) {
        $this->ubicacionevento = $ubicacionevento;
    }

    function setDescuentosevento($descuentosevento) {
        $this->descuentosevento = $descuentosevento;
    }

    public function expose() {
        return get_object_vars($this);
    }
}
