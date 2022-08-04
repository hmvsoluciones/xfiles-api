<?php

interface ReportsDao {

    
    /**
     * Consulta de registro de tabla demo
     * 
     * @param Integer [id] id de la tabla demo
     * @return Array Objeto por id
     */
    public function getBoletosVendidos($id);

    /**
     * @param String [sql], Consulta SQL generica para reportes genericos
     * @return String Tabla HTML para reportes dinamicos
     */
    public function getHTMLTableReport($sql);

    public function serchOrdenCompra($ordenCompra);

}
