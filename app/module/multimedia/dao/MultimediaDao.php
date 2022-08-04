<?php

interface MultimediaDao {

      /**
     * INSERT en tabla multimedia
     * 
     * @param Object [$obj] objeto valores id y textValue
     * @return boolean Boolean
     */
    public function add($multimedia);
    
    
    /**
     * Eliminar registro de tabla multimedia
     * 
     * @param Integer [idRemote] id de la tabla demo
     * @return Boolean respuesta de ejecución
     */
    public function delete($idRemote);

    /**
     * Consulta de registro de tabla demo
     * 
     * @param Integer [id] id de la tabla demo
     * @return Array Objeto por id
     */
    public function get($id);
}
