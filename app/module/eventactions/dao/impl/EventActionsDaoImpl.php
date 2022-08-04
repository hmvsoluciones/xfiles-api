<?php

class EventActionsDaoImpl extends Connection implements EventActionsDao{

    private $util;

    function __construct()
    {
        $this->util = new UtilImpl();
    }

    public function like($idEvento, $idUser)
    {
        $query = "INSERT INTO usuarioslikeevento
        (        
          idevento,
          idusuario
        )
        VALUES
        (         
          {$idEvento},
          {$idUser}
        );";

        if ($this->executeQuery($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function unlike($idEvento, $idUser)
    {
        $query = "DELETE FROM usuarioslikeevento WHERE idevento = {$idEvento} AND idusuario={$idUser}";
        
        if ($this->executeDelete($query)) {            
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
