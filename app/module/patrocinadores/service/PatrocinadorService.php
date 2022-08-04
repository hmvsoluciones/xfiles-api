<?php

interface PatrocinadorService {
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
     * Creacion de funcion de negocio para obtener registros de patrocinadores asociados a un evento
     * 
     * @param Integer [id] id del evento
     * @return Object WSResponse
     */
    public function getByEvent($id);

     /**
     * Agrega una relación de evento patrocinador
     * 
     * @param Integer [id] id de evento
     * @param Integer [id] id de patrocinador
     * @return Array Objeto por id
     */
    public function createEventoPatrocinador($idEvento, $idPatrocinador, $idUsuarioAlta);

         /**
     * Agrega una relación de evento patrocinador
     * 
     * @param Integer [id] id de eventopatrocinador
     */
    public function deleteEventoPatrocinador($idEventoPatrocinador);

    /**
     * Creacion de funcion de negocio para agregar registro
     *      
     * @return Object WSResponse
     */
    public function getAllData();

    
    /**
     * Creacion de funcion de negocio para agregar registro
     *      
     * @return Object WSResponse
     */
    public function getAllDataByUserId($id);

    /**
     * Creacion de funcion de negocio para eliminar registro por id
     * 
     * @param Integer [id] id de la tabla demo
     * @return Object WSResponse
     */
    public function delete($id);

    public function getAllPatrocinadoresToAddByUserId($id, $idEvento);

    public function getIdMultimediaByIdPatrocinador($idPatrocinador);
}
