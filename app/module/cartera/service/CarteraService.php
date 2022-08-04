<?php

interface CarteraService {
    /**
     * Creacion de funcion de negocio para agregar registro
     * 
     * @param Object [$demo] objeto valores id y textValue
     * @return Object WSResponse
     */
    public function getPuntos($idUsuario);
    public function setPuntosToUser($idUsuario, $puntos);
    public function getAbonos($idUsuario);
}
