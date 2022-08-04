<?php

interface BoletosDao {
  
    public function generateFolio($idEvento);

    public function loadKardexBoletos($idTipoBoleto, $folio, $asiento, $nombre, $correo, $telefono, $facebook=null, $instagram=null, $estatus);

    //boletos fisicos
    
    public function loadBoletosFisicos($numeroBoletosImpresos, $idUsuario, $idTipoBoleto);

    public function loadFisicoKardex($idBoletosFisicos, $idKardexBoletos);
    
    //Cortesias
    public function loadCortesia($nombrePersonaCortesia, $correoPersonaCortesia, $telefonoPersonaCortesia, $numeroBoletosDisponibles, $motivo, $idTipoBoleto, $idUsuaalta);

    public function loadCortesiaKardex($idCortesia, $idKardex);

    
    //Lista invitados
    public function loadListaInvitadosKardex($idListaInvitados, $idKardex);
    
    public function loadListaInvitados($nombreInvitadoPrincipal,$telefonoInvitadoPrincipal,$correoInvitadoPrincipal, $numeroBoletosDisponibles, $idTipoBoleto, $urlGenBoletos,$idUsuAlta);

    public function getBoletosPrecioPublico($idUsuario);
    public function getBoletosFisicos($idTipoBoleto);
    public function getAllListaInvidatosByEvento($Idevento);
    public function getBoletosCreadosEvento($idEvento);
    public function getCupoEvento($idEvento);
    public function getBoletosCreadosTipoBoleto($idtipoBoleto);
    public function getCupoTipoBoleto($idtipoBoleto);
    
    public function getalltipoboletos($idEvento);

    //regresa cantidad maximo de boletos por tipo de boletos
    public function getMaximoBoletos($idTipoBoleto);

    //regresa cantidad de boletos registraod en kardex boletos(boletos comprados y/o no disponibles)
    public function getBoletosInKardexByTipoBoleto($idTipoBoleto);

    public function getallInicioEvento($idEvento);
    
    public function getListaKardexBoletosFisicos($idTipoBoleto, $idBoletosFisicos);
    public function getListaKardexBoletosLista($idTipoBoleto, $idBoletosFisicos);
    public function getListaKardexBoletosCortesia($idTipoBoleto, $idBoletosFisicos);

    public function getBoletosByIdListaInvitados($idListaInvitados);
    public function getListaInvitadosByIdLista($idListaInvitados);

    public function updateKardex($params);

    public function getIdListaKardexByKardex($idKardex);
   
    public function getFolioBoletoByKardex($idKardex);

    public function getCortesias($idTipoBoleto);

    public function getBoletoInfo($folioBoleto);

    public function getListaKardexOrdenCompra($idOrdenCompra);

    public function updateStatusKardexBoletosAfterPaying($idReferencia);

    public function getContactInfoToNotifyOnFullPayment($idReferenciaPago);

    public function getIdTransaccionByOrdenCompra($idOrdenCompra);

    public function cancelarOrdenCompra($idOrdenCompra);

    public function cancelarKardexBoletosByOrdenCompra($idOrdenCompra);

    public function esCortesiaOrListaInvitados($folioBoleto);

    public function getLogosProductorByIdEvento($idEvento);
}
