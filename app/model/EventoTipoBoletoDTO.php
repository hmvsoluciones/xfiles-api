<?php

class EventoTipoBoletoDTO extends AuditoriaDTO {    
    private $idTipoBoleto;
    private $idEvento;
    private $tipoBoleto;
    private $tipoBoletoDesc;
    private $cantidadBoletos;
    private $precio;
    
    function getIdTipoBoleto() {
        return $this->idTipoBoleto;
    }
    function getIdEvento() {
        return $this->idEvento;
    }

    function getTipoBoleto() {
        return $this->tipoBoleto;
    }

    function getTipoBoletoDesc() {
        return $this->tipoBoletoDesc;
    }

    function getCantidadBoletos() {
        return $this->cantidadBoletos;
    }

    function getPrecio() {
        return $this->precio;
    }

    function setIdTipoBoleto($idTipoBoleto) {
        $this->idTipoBoleto = $idTipoBoleto;
    }

    function setIdEvento($idEvento) {
        $this->idEvento = $idEvento;
    }

    function setTipoBoleto($tipoBoleto) {
        $this->tipoBoleto = $tipoBoleto;
    }

    function setTipoBoletoDesc($tipoBoletoDesc) {
        $this->tipoBoletoDesc = $tipoBoletoDesc;
    }

    function setCantidadBoletos($cantidadBoletos) {
        $this->cantidadBoletos = $cantidadBoletos;
    }

    function setPrecio($precio) {
        $this->precio = $precio;
    }

     public function expose() {
        return get_object_vars($this);
    }
}
