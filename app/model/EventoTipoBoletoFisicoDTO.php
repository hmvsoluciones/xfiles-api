<?php
class EventoTipoBoletoFisicoDTO extends AuditoriaDTO {
    private $idEvento;
    private $idTipoBoleto;
    private $idBoletoFisico;
    private $cantidad;

    function getIdEvento() {
        return $this->idEvento;
    }

    function getIdTipoBoleto() {
        return $this->idTipoBoleto;
    }

    function getIdBoletoFisico() {
        return $this->idBoletoFisico;
    }

    function getCantidad() {
        return $this->cantidad;
    }

    function setIdEvento($idEvento) {
        $this->idEvento = $idEvento;
    }

    function setIdTipoBoleto($idTipoBoleto) {
        $this->idTipoBoleto = $idTipoBoleto;
    }

    function setIdBoletoFisico($idBoletoFisico) {
        $this->idBoletoFisico = $idBoletoFisico;
    }

    function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    } 

    public function expose() {
        return get_object_vars($this);
    }

}
