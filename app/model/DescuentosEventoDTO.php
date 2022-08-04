<?php
class DescuentosEventoDTO extends AuditoriaDTO {
    private $idDescuentoEvento;
    private $nombreDescuento;
    private $condicionesAplicacionDescuento;
    private $porcentajeMontoDescuento;
    private $idEvento;
    private $claveCupon;
    

    /**
     * Get the value of idDescuentoEvento
     */ 
    public function getIdDescuentoEvento()
    {
        return $this->idDescuentoEvento;
    }

    /**
     * Set the value of idDescuentoEvento
     *
     * @return  self
     */ 
    public function setIdDescuentoEvento($idDescuentoEvento)
    {
        $this->idDescuentoEvento = $idDescuentoEvento;

        return $this;
    }

    /**
     * Get the value of nombreDescuento
     */ 
    public function getNombreDescuento()
    {
        return $this->nombreDescuento;
    }

    /**
     * Set the value of nombreDescuento
     *
     * @return  self
     */ 
    public function setNombreDescuento($nombreDescuento)
    {
        $this->nombreDescuento = $nombreDescuento;

        return $this;
    }

    /**
     * Get the value of condicionesAplicacionDescuento
     */ 
    public function getCondicionesAplicacionDescuento()
    {
        return $this->condicionesAplicacionDescuento;
    }

    /**
     * Set the value of condicionesAplicacionDescuento
     *
     * @return  self
     */ 
    public function setCondicionesAplicacionDescuento($condicionesAplicacionDescuento)
    {
        $this->condicionesAplicacionDescuento = $condicionesAplicacionDescuento;

        return $this;
    }

    /**
     * Get the value of porcentajeMontoDescuento
     */ 
    public function getPorcentajeMontoDescuento()
    {
        return $this->porcentajeMontoDescuento;
    }

    /**
     * Set the value of porcentajeMontoDescuento
     *
     * @return  self
     */ 
    public function setPorcentajeMontoDescuento($porcentajeMontoDescuento)
    {
        $this->porcentajeMontoDescuento = $porcentajeMontoDescuento;

        return $this;
    }

    /**
     * Get the value of idTipoDescuento
     */ 
    public function getClaveCupon()
    {
        return $this->claveCupon;
    }

    /**
     * Set the value of idTipoDescuento
     *
     * @return  self
     */ 
    public function setClaveCupon($claveCupon)
    {
        $this->claveCupon = $claveCupon;

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
}