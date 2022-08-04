<?php

class UsuariosRelPublicDTO extends AuditoriaDTO {

    private $idRelacionesPublicas;
      private $idUsuario;
    private $idEvento;

    function getIdUsuarioRelPublic() {
        return $this->idRelacionesPublicas;
    }

    function getIdUsuario() {
        return $this->idUsuario;
    }

    function getIdEvento() {
        return $this->idEvento;
    }

    function setIdUsuarioRelPublic($idRelacionesPublicas) {
        $this->idRelacionesPublicas = $idRelacionesPublicas;
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
