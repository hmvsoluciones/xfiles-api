<?php

interface OrdenCompraDao {

      /**
     * INSERT en tabla demo
     * 
     * @param OrdenCompraDTO [$ordenCompra] objeto valores id y textValue
     * @return boolean Boolean
     */
    public function add($ordenCompra);
    
    /**
     * Actualización de tabla demo
     * 
     * @param Object [$ordenCompra] objeto valores id y textValue
     * @return boolean Boolean
     */
    public function update($ordenCompra);

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
