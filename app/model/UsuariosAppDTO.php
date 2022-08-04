<?php

class UsuariosAppDTO extends AuditoriaDTO {

    private $idUsuarioApp;
    private $nip;
    private $idUsuario;
    private $idEvento;

    function getIdUsuarioApp() {
        return $this->idUsuarioApp;
    }

    function getNip() {
        return $this->nip;
    }

    function getIdUsuario() {
        return $this->idUsuario;
    }

    function getIdEvento() {
        return $this->idEvento;
    }

    function setIdUsuarioApp($idUsuarioApp) {
        $this->idUsuarioApp = $idUsuarioApp;
    }

    function setNip($nip) {
        $this->nip = $nip;
    }

    function setIdUsuario($idUsuario) {
        $this->idUsuario = $idUsuario;
    }

    function setIdEvento($idEvento) {
        $this->idEvento = $idEvento;
    }

    public function expose() {
        return get_object_vars($this);
    }

}
