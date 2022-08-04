<?php

interface MovilDao {

    public function getEventsForPuntoVentaUser($idUser);

    public function getEventsForAppMovilUser($idUser);

    /**
     * Retorna evento si es boleto fisico
     */
    public function esBoletoFisicoValido($idEvento, $idUsuario, $folioBoleto);

    /**
     * Retorna boleto si es valido para un usuario y un evento
     */
    public function esBoletoPaseEventoValido($idEvento, $idUsuario, $folioBoleto);

    public function activarBoletoFisico($folioBoleto);

    public function paseEvento($folioBoleto);

    public function esCortesiaByFolioBoleto($folioBoleto);

    public function esListaInvitadosByFolioBoleto($folioBoleto);

    public function paseEventoConMedioBoleto($folioBoleto);

    public function esValidoPagoSinMedioBoleto($fechaEvento, $horaPagoMedioBoletoEvento);

    public function getKardexByFolioBoleto($folioBoleto);

    public function activarBoletoFisicoGlobal($folioBoleto, $idUsuarioActiva);

    public function paseEventoGlobal($folioBoleto, $idUsuarioIngreso);

    public function paseEventoConMedioBoletoGlobal($folioBoleto, $idUsuarioIngreso);

    public function getFechaHoraFinListaCortesia($folioBoleto);
}
