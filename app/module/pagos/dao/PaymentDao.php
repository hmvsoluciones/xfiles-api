<?php



interface PaymentDao {
    public function loadOrdenCompra($params);

    public function loadDetalleOrdenCompra($params);
    
    public function setBoletosPagadosByIdReferencia($idReferenciaPago);
}