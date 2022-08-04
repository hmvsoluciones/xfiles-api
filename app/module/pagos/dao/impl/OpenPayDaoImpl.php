<?php


use Openpay\Data\Openpay;
use OpenPay\Resources\OpenPayCharge;

class OpenPayDaoImpl implements OpenPayDao {

    
    
    /**
     * Agrega un pago
     * 
     * @param CargoDTO [$cargoDTO] objeto tipo cargo sin id
     * @return CargoDTO 
     */
    public function makeAPay($params){
        $config = parse_ini_file(__DIR__ . '../../../../../config/openpay.ini');
        try {
            //cambiar a true cuando se pase  producción
            Openpay::setProductionMode($config["productionMode"]); 
            //$openpay = Openpay::getInstance('m9tjc0nksqpoivgkljwa', 'sk_c746472a060d4b2f817ea6150cf76798', 'MX');
            $openpay = Openpay::getInstance($config["id"], $config["apiKey"], $config["country"]);
    
            
            return $openpay->charges->create($params);
            
        } catch (Exception $e) {
            return "Ocurrio un error: ".$e->getMessage();
        }   
       
    }

     /**
     * Consulta un pago 
     * 
     * @param Integer [id] id del pago
     * @return CargoDTO el objeto Cargo
     */
    public function getPayStatus($id) {
        $config = parse_ini_file(__DIR__ . '../../../../../config/openpay.ini');
        try {

            Openpay::setProductionMode($config["productionMode"]); 
            
            $openpay = Openpay::getInstance($config['id'], $config['apiKey'], $config['country']);                        
            
            return $openpay->charges->get($id);             
   
       } catch (Exception $e) {
               return "Ocurrio un error ".$e->getMessage();   
       } 

    }
}
