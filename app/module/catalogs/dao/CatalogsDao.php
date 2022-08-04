<?php

interface CatalogsDao {

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
     * Consulta de todos los registros de tabla demo
     *      
     * @return Array Objetos de toda la tabla
     */
    public function getAllData();

    /**
     * Elimar registro de tabla
     * 
     * @param Integer [id] id de la tabla demo
     * @return Boolean respuesta de ejecución
     */
    public function delete($id);
}
