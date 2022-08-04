<?php
class UbicacionEventoDTO extends AuditoriaDTO {
    private $idUbicacionEvento;
    private $direccionUbicacionEvento;
    private $lugar;
    private $urlUbicacionEvento;
    private $referenciasUbicacionEvento;
    private $notasUbicacionEvento;
    private $iFrameMapsUbicacionEvento;
    private $idEvento;
    private $urlImagen;
    
    public function getLugar()
    {
        return $this->lugar;
    }

    /**
     * Get the value of idUbicacionEvento
     */ 
    public function getIdUbicacionEvento()
    {
        return $this->idUbicacionEvento;
    }

    public function setLugar($lugar)
    {
        $this->lugar = $lugar;

        return $this;
    }
    /**
     * Set the value of idUbicacionEvento
     *
     * @return  self
     */ 
    public function setIdUbicacionEvento($idUbicacionEvento)
    {
        $this->idUbicacionEvento = $idUbicacionEvento;

        return $this;
    }

    /**
     * Get the value of direccionUbicacionEvento
     */ 
    public function getDireccionUbicacionEvento()
    {
        return $this->direccionUbicacionEvento;
    }

    /**
     * Set the value of direccionUbicacionEvento
     *
     * @return  self
     */ 
    public function setDireccionUbicacionEvento($direccionUbicacionEvento)
    {
        $this->direccionUbicacionEvento = $direccionUbicacionEvento;

        return $this;
    }

    /**
     * Get the value of urlUbicacionEvento
     */ 
    public function getUrlUbicacionEvento()
    {
        return $this->urlUbicacionEvento;
    }

    /**
     * Set the value of urlUbicacionEvento
     *
     * @return  self
     */ 
    public function setUrlUbicacionEvento($urlUbicacionEvento)
    {
        $this->urlUbicacionEvento = $urlUbicacionEvento;

        return $this;
    }

    /**
     * Get the value of referenciasUbicacionEvento
     */ 
    public function getReferenciasUbicacionEvento()
    {
        return $this->referenciasUbicacionEvento;
    }

    /**
     * Set the value of referenciasUbicacionEvento
     *
     * @return  self
     */ 
    public function setReferenciasUbicacionEvento($referenciasUbicacionEvento)
    {
        $this->referenciasUbicacionEvento = $referenciasUbicacionEvento;

        return $this;
    }

    /**
     * Get the value of notasUbicacionEvento
     */ 
    public function getNotasUbicacionEvento()
    {
        return $this->notasUbicacionEvento;
    }

    /**
     * Set the value of notasUbicacionEvento
     *
     * @return  self
     */ 
    public function setNotasUbicacionEvento($notasUbicacionEvento)
    {
       
        $this->notasUbicacionEvento = $notasUbicacionEvento;

        return $this;
    }

    /**
     * Get the value of iFrameMapsUbicacionEvento
     */ 
    public function getIFrameMapsUbicacionEvento()
    {
        return $this->iFrameMapsUbicacionEvento;
    }

    /**
     * Set the value of iFrameMapsUbicacionEvento
     *
     * @return  self
     */ 
    public function setIFrameMapsUbicacionEvento($iFrameMapsUbicacionEvento)
    {
        $this->iFrameMapsUbicacionEvento = $iFrameMapsUbicacionEvento;

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
     * Get the value of url de imagen
     */ 
    public function getUrlImagen()
    {
        return $this->urlImagen;
    }

    /**
     * Set the value of url imagen
     *
     * @return  self
     */ 
    public function setUrlImagen($urlImagen)
    {
        $this->urlImagen = $urlImagen;

        return $this;
    }

}