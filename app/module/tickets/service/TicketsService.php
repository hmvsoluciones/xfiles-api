<?php

interface TicketsService {
    /**
     * Creacion de funcion de negocio para agregar registro
     * 
     * @param Object [$demo] objeto valores id y textValue
     * @return Object WSResponse
     */
    public function add($demo);
    
    /**
     * Creacion de funcion de negocio para actualizar registro
     * 
     * @param Object [$demo] objeto valores id y textValue
     * @return Object WSResponse
     */
    public function update($demo);

    /**
     * Creacion de funcion de negocio para obtener registro por id
     * 
     * @param Integer [id] id de la tabla demo
     * @return Object WSResponse
     */
    public function get($id);

    /**
     * Creacion de funcion de negocio para agregar registro
     *      
     * @return Object WSResponse
     */
    public function getAllData();

    /**
     * Creacion de funcion de negocio para eliminar registro por id
     * 
     * @param Integer [id] id de la tabla demo
     * @return Object WSResponse
     */
    public function delete($id);
}
