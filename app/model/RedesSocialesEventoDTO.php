<?php
class RedesSocialesEventoDTO extends AuditoriaDTO {
    private $idRedSocialEvento;
    private $urlRedSocial;
    private $idEvento;
    private $idTipoRedSocial;
    

    /**
     * Get the value of idRedSocialEvento
     */ 
    public function getIdRedSocialEvento()
    {
        return $this->idRedSocialEvento;
    }

    /**
     * Set the value of idRedSocialEvento
     *
     * @return  self
     */ 
    public function setIdRedSocialEvento($idRedSocialEvento)
    {
        $this->idRedSocialEvento = $idRedSocialEvento;

        return $this;
    }

    /**
     * Get the value of urlRedSocial
     */ 
    public function getUrlRedSocial()
    {
        return $this->urlRedSocial;
    }

    /**
     * Set the value of urlRedSocial
     *
     * @return  self
     */ 
    public function setUrlRedSocial($urlRedSocial)
    {
        $this->urlRedSocial = $urlRedSocial;

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
     * Get the value of idTipoRedSocial
     */ 
    public function getIdTipoRedSocial()
    {
        return $this->idTipoRedSocial;
    }

    /**
     * Set the value of idTipoRedSocial
     *
     * @return  self
     */ 
    public function setIdTipoRedSocial($idTipoRedSocial)
    {
        $this->idTipoRedSocial = $idTipoRedSocial;

        return $this;
    }
}