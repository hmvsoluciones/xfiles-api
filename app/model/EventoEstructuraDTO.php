<?php

class EventoEstructuraDTO extends AuditoriaDTO {
    private $idEventoEstructura;
    private $idTipoElemento;
    private $idMultimedia;
    private $idEvento;
    private $idUsuAlta;
    private $fechaAlta;
    private $idUsuModifica;
    private $fechaModifica;
    private $estatus;
    
    public function expose() {
        return get_object_vars($this);
    }

    /**
     * Get the value of idEventoEstructura
     */ 
    public function getIdEventoEstructura()
    {
        return $this->idEventoEstructura;
    }

    /**
     * Set the value of idEventoEstructura
     *
     * @return  self
     */ 
    public function setIdEventoEstructura($idEventoEstructura)
    {
        $this->idEventoEstructura = $idEventoEstructura;

        return $this;
    }

    /**
     * Get the value of idTipoElemento
     */ 
    public function getIdTipoElemento()
    {
        return $this->idTipoElemento;
    }

    /**
     * Set the value of idTipoElemento
     *
     * @return  self
     */ 
    public function setIdTipoElemento($idTipoElemento)
    {
        $this->idTipoElemento = $idTipoElemento;

        return $this;
    }

    /**
     * Get the value of idMultimedia
     */ 
    public function getIdMultimedia()
    {
        return $this->idMultimedia;
    }

    /**
     * Set the value of idMultimedia
     *
     * @return  self
     */ 
    public function setIdMultimedia($idMultimedia)
    {
        $this->idMultimedia = $idMultimedia;

        return $this;
    }

    /**
     * Get the value of idEvento
     */ 
    public function getIdEvento()
    {
        return $this->idEvento;
    }

    /**
     * Set the value of idEvento
     *
     * @return  self
     */ 
    public function setIdEvento($idEvento)
    {
        $this->idEvento = $idEvento;

        return $this;
    }

    /**
     * Get the value of idUsuAlta
     */ 
    public function getIdUsuAlta()
    {
        return $this->idUsuAlta;
    }

    /**
     * Set the value of idUsuAlta
     *
     * @return  self
     */ 
    public function setIdUsuAlta($idUsuAlta)
    {
        $this->idUsuAlta = $idUsuAlta;

        return $this;
    }

    /**
     * Get the value of fechaAlta
     */ 
    public function getFechaAlta()
    {
        return $this->fechaAlta;
    }

    /**
     * Set the value of fechaAlta
     *
     * @return  self
     */ 
    public function setFechaAlta($fechaAlta)
    {
        $this->fechaAlta = $fechaAlta;

        return $this;
    }

    /**
     * Get the value of idUsuModifica
     */ 
    public function getIdUsuModifica()
    {
        return $this->idUsuModifica;
    }

    /**
     * Set the value of idUsuModifica
     *
     * @return  self
     */ 
    public function setIdUsuModifica($idUsuModifica)
    {
        $this->idUsuModifica = $idUsuModifica;

        return $this;
    }

    /**
     * Get the value of fechaModifica
     */ 
    public function getFechaModifica()
    {
        return $this->fechaModifica;
    }

    /**
     * Set the value of fechaModifica
     *
     * @return  self
     */ 
    public function setFechaModifica($fechaModifica)
    {
        $this->fechaModifica = $fechaModifica;

        return $this;
    }

    /**
     * Get the value of estatus
     */ 
    public function getEstatus()
    {
        return $this->estatus;
    }

    /**
     * Set the value of estatus
     *
     * @return  self
     */ 
    public function setEstatus($estatus)
    {
        $this->estatus = $estatus;

        return $this;
    }
}