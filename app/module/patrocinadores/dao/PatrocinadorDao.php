<?php

interface PatrocinadorDao {

      /**
     * INSERT en tabla demo
     * 
     * @param Object [$obj] objeto valores id y textValue
     * @return boolean Boolean
     */
    public function add($obj);
    
    /**
     * Actualización de tabla demo
     * 
     * @param Object [$obj] objeto valores id y textValue
     * @return boolean Boolean
     */
    public function update($obj);

    /**
     * Consulta de registro de tabla demo
     * 
     * @param Integer [id] id de la tabla demo
     * @return Array Objeto por id
     */
    public function get($id);

       /**
     * Consulta de registros de patrocinaores asociados a un evento
     * 
     * @param Integer [id] id de la tabla demo
     * @return Array Objeto por id
     */
    public function getByEvent($id);

      /**
     * Agrega una relación de evento patrocinador
     * 
     * @param Integer [id] id de evento
     * @param Integer [id] id de patrocinador
     * @param Integer [id] id de usuario alta
     */
    public function createEventoPatrocinador($idEvento, $idPatrocinador, $idUsuarioAlta);

    
      /**
     * Elimina una relación de evento patrocinador
     * 
     * @param Integer [id] id de eventopatrocinador
     */
    public function deleteEventoPatrocinador($idEventoPatrocinador);

    /**
     * Consulta de todos los registros de tabla demo
     *      
     * @return Array Objetos de toda la tabla
     */
    public function getAllData();

        /**
     * Consulta de todos los registros de tabla demo
     *      
     * @return Array Objetos de toda la tabla
     */
    public function getAllDataByUserId($id);

    /**
     * Elimar registro de tabla
     * 
     * @param Integer [id] id de la tabla demo
     * @return Boolean respuesta de ejecución
     */
    public function delete($id);

    public function getAllPatrocinadoresToAddByUserId($id, $idEvento);

    public function getIdMultimediaByIdPatrocinador($idPatrocinador);
}
