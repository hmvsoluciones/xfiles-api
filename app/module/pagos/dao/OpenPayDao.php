<?php



interface OpenPayDao {
     /**
     * Agrega un pago
     * 
     * @param CargoDTO [$cargoDTO] objeto tipo cargo sin id
     * @return CargoDTO 
     */
    public function makeAPay($cargoDTO);
    

     /**
     * Consulta un pago 
     * 
     * @param Integer [id] id del pago
     * @return CargoDTO el objeto Cargo
     */
    public function getPayStatus($id);
}