<?php

interface MovilService {
    /**
     * Creacion de funcion de negocio para agregar registro
     * 
     * @param idUser 
     * @param role PUNTO DE VENTA o APP MOVIL
     * @return Object WSResponse
     */
    public function getEventsAppMovil($idUser, $role);

    public function activarTicket($idUser, $idEvent, $folioBoleto);

    public function paseEventoTicket($idUser, $idEvent, $folioBoleto);

    public function activarTicketGlobal($idUser, $folioBoleto);

    public function paseEventoTicketGlobal($idUser, $folioBoleto);
    
}
