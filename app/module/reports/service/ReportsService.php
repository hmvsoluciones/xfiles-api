<?php

interface ReportsService {
   

    /**
     * Creacion de funcion de negocio para obtener registro por id
     * 
     * @param Integer [id] id de la tabla demo
     * @return Object WSResponse
     */
    public function getBoletosVendidos($id);

    public function getHTMLTableReport($sql);

    public function serchOrdenCompra($ordenCompra);
}
