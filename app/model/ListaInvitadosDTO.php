<?php
class ListaInvitadosDTO extends AuditoriaDTO {
    private $idlistainvitados;
    private $nombreinvitadoprincipal;
    private $telefonoinvitadoprincipal; 
    private $correoinvitadoprincipal;
    private $instagraminvitadoprincipal;
    private $idtipoboleto;
    private $numeroboletosdisponibles;
    private $horafingratuito;
    private $idevento;
    private $horafinmedioboleto;
    private $urlgenboletos;
    private $idusuario;
   
    public function getIdListaInvitados()
    {
        return $this->idlistainvitados; 
    }

  
    public function setIdListaInvitados($idlistainvitados)
    {
        $this->idlistainvitados = $idlistainvitados;

        return $this;
    }

    public function getNombreInvitadoPrincipal()
    {
        return $this->nombreinvitadoprincipal;
    }

    public function setNombreInvitadoPrincipal($nombreinvitadoprincipal)
    {
        $this->nombreinvitadoprincipal = $nombreinvitadoprincipal;

        return $this;
    }

    public function getTelefonoInvitadoPrincipal()
    {
        return $this->telefonoinvitadoprincipal;
    }

  
    public function setTelefonoInvitadoPrincipal($telefonoinvitadoprincipal)
    {
        $this->telefonoinvitadoprincipal = $telefonoinvitadoprincipal;

        return $this;
    }

    public function getCorreoInvitadoPrincipal()
    {
        return $this->correoinvitadoprincipal;
    }

    public function setCorreoInvitadoPrincipal($correoinvitadoprincipal)
    {
        $this->correoinvitadoprincipal = $correoinvitadoprincipal;

        return $this;
    }

    public function getInstagramInvitadoPrincipal()
    {
        return $this->instagraminvitadoprincipal;
    }

    public function setInstagramInvitadoPrincipal($instagraminvitadoprincipal)
    {
        $this->instagraminvitadoprincipal = $instagraminvitadoprincipal;

        return $this;
    }

    public function getIdTipoBoleto()
    {
        return $this->idtipoboleto;
    }

    public function setIdTipoBoleto($idtipoboleto)
    {
        $this->clavepidtipoboletoroductor = $idtipoboleto;

        return $this;
    }

    public function getHoraFinGratuito()
    {
        return $this->horafingratuito;
    }

    public function setHoraFinGratuito($horafingratuito)
    {
        $this->horafingratuito = $horafingratuito;

        return $this;
    }

    public function getNumeroBoletosDisponibles()
    {
        return $this->numeroboletosdisponibles;
    }

    public function setNumeroBoletosGenerados($numeroboletosgenerados)
    {
        $this->numeroboletosgenerados = $numeroboletosgenerados;

        return $this;
    }

    public function getHoraFinMedioBoleto()
    {
        return $this->horafinmedioboleto;
    }


    public function setHoraFinMedioBoleto($horafinmedioboleto)
    {
        $this->numeroboletosdishorafinmedioboletoponibles = $horafinmedioboleto;

        return $this;
    }

    public function getUrlGenboletos()
    {
        return $this->urlgenboletos;
    }

    public function setUrlGenboletos($urlgenboletos)
    {
        $this->urlgenboletos = $urlgenboletos;

        return $this;
    }

    public function getIdEvento()
    {
        return $this->idevento;
    }

    public function setIdEvento($idevento)
    {
        $this->idevento = $idevento;

        return $this;
    }

    public function getIdUsuario()
    {
        return $this->idusuario;
    }

    public function setIdUsuario($idusuario)
    {
        $this->idusuario = $idusuario;

        return $this;
    }
}