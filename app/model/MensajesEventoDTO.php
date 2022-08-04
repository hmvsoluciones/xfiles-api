<?php
class MensajesEventoDTO extends AuditoriaDTO {
    private $idMensajeEvento;
    private $sms;
    private $correo;
    private $notas;
    private $idevento;


    // private $idMensajeEvento;
    // private $nombreMensaje;
    // private $cuerpoMensaje;
    // private $idCatMensaje;
    // private $idCatEnvio;
    // private $idEvento;
    
    public function expose() {
        return get_object_vars($this);
    }

    /**
     * Get the value of idMensajeEvento
     */ 
    public function getIdMensajeEvento()
    {
        return $this->idMensajeEvento;
    }

    /**
     * Set the value of idMensajeEvento
     *
     * @return  self
     */ 
    public function setIdMensajeEvento($idMensajeEvento)
    {
        $this->idMensajeEvento = $idMensajeEvento;

        return $this;
    }
     /**
     * Get the value of sms
     */ 
    public function getSms()
    {
        return $this->sms;
    }

    /**
     * Set the value of sms
     *
     * @return  self
     */ 
    public function setSms($sms)
    {
        $this->sms = $sms;

        return $this;
    }
     /**
     * Get the value of correo
     */ 
    public function getCorreo()
    {
        return $this->correo;
    }

    /**
     * Set the value of correo
     *
     * @return  self
     */ 
    public function setCorreo($correo)
    {
        $this->correo = $correo;

        return $this;
    }
     /**
     * Get the value of notas
     */ 
    public function getNotas()
    {
        return $this->notas;
    }

    /**
     * Set the value of notas
     *
     * @return  self
     */ 
    public function setNotas($notas)
    {
        $this->notas = $notas;

        return $this;
    }
    // /**
    //  * Get the value of nombreMensaje
    //  */ 
    // public function getNombreMensaje()
    // {
    //     return $this->nombreMensaje;
    // }

    // /**
    //  * Set the value of nombreMensaje
    //  *
    //  * @return  self
    //  */ 
    // public function setNombreMensaje($nombreMensaje)
    // {
    //     $this->nombreMensaje = $nombreMensaje;

    //     return $this;
    // }

    // /**
    //  * Get the value of cuerpoMensaje
    //  */ 
    // public function getCuerpoMensaje()
    // {
    //     return $this->cuerpoMensaje;
    // }

    // /**
    //  * Set the value of cuerpoMensaje
    //  *
    //  * @return  self
    //  */ 
    // public function setCuerpoMensaje($cuerpoMensaje)
    // {
    //     $this->cuerpoMensaje = $cuerpoMensaje;

    //     return $this;
    // }

    // /**
    //  * Get the value of idCatMensaje
    //  */ 
    // public function getIdCatMensaje()
    // {
    //     return $this->idCatMensaje;
    // }

    // /**
    //  * Set the value of idCatMensaje
    //  *
    //  * @return  self
    //  */ 
    // public function setIdCatMensaje($idCatMensaje)
    // {
    //     $this->idCatMensaje = $idCatMensaje;

    //     return $this;
    // }

    // /**
    //  * Get the value of idCatEnvio
    //  */ 
    // public function getIdCatEnvio()
    // {
    //     return $this->idCatEnvio;
    // }

    // /**
    //  * Set the value of idCatEnvio
    //  *
    //  * @return  self
    //  */ 
    // public function setIdCatEnvio($idCatEnvio)
    // {
    //     $this->idCatEnvio = $idCatEnvio;

    //     return $this;
    // }

    /**
     * Get the value of idEvento
     */ 
    public function getIdEvento()
    {
        return $this->idevento;
    }

    /**
     * Set the value of idEvento
     *
     * @return  self
     */ 
    public function setIdEvento($idevento)
    {
        $this->idevento = $idevento;

        return $this;
    }
}