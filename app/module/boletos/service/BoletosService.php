<?php

interface BoletosService {

    public function loadKardexBoletos(
        $idEvento, 
        $idTipoBoleto, 
        $cantidadBoletos, 
        $nombre , 
        $correo, 
        $telefono,  
        $facebook, 
        $instagram,
        $estatus
    );

    public function addBoletosFisicos(
        $numeroBoletosImpresos, 
        $idUsuario,
        $idTipoBoleto, 
        $idEvento
    );

    public function addCortesias(
        $nombrePersonaCortesia, 
        $correoPersonaCortesia, 
        $telefonoPersonaCortesia, 
        $numeroBoletos, 
        $motivocortesia,
        $idTipoBoleto, 
        $idUsuAlta,
        $idEvento
    );
    public function getBoletosPrecioPublico($idUsuario);
    public function getBoletosFisicos($idTipoBoleto);
    public function getTipoBoletos($idevento);
    public function getTipoBoletosDisponibles($idevento);

    public function getInicioEvento($idevento);
    
    public function getBoletosList($tipo, $idTipoBoleto, $idReferencia);
    public function addListaInvitados( $nombreInvitadoPrincipal, $telefonoInvitadoPrincipal, $correoInvitadoPrincipal, 
        $numeroBoletosDisponibles, $idTipoBoleto,$urlGenBoletos,$idUsuAlta,$idEvento);

    public function getBoletosByIdListaInvitados($idListaInvitados);
    public function getListaInvitadosByIdLista($idListaInvitados);

    public function getKardex($idKardex);
    /*$parsedBody['idkardexboletos'],                         
        $parsedBody['nombrepersona'],
        $parsedBody['correopersona'],
        $parsedBody['telefonopersona'], 
        $parsedBody['facebookpersona'], 
        $parsedBody['instagrampersona'],            
        $parsedBody['estatus']*/
    public function updateKardex($params);

    public function getCortesias($idTipoBoleto);

    public function getBoletoInfo($folioBoleto);

    public function updateStatusKardexBoletosAfterPaying($idReferencia);

    public function getContactInfoToNotifyOnFullPayment($idReferenciaPago);

    public function getIdTransaccionByOrdenCompra($idOrdenCompra);

    public function cancelarOrdenCompra($idOrdenCompra);

    public function esCortesiaOrListaInvitados($folioBoleto);

    public function getLogosProductorByIdEvento($idEvento);
}
