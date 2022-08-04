<?php

class CarteraDaoImpl extends Connection implements CarteraDao
{
    private $util;

    public function __construct()
    {
        $this->util = new UtilImpl();
    }

   
    public function getPuntos($idUsuario)
    {
        $query = "SELECT puntosdisponibles FROM cartera WHERE idusuario = {$idUsuario}";
        return $this->getAll($query);
    }

    public function getCartera($idUsuario)
    {

        $query = "SELECT idcartera, puntosdisponibles, idusuario FROM cartera WHERE idusuario = {$idUsuario}";

        return $this->getAll($query);
    }

    public function createCartera($puntosDisponibles, $idUsuario)
    {
        $query = "INSERT INTO cartera
        (
          puntosdisponibles,
          idusuario
        )
        VALUES
        (
          {$puntosDisponibles},
          {$idUsuario}
        ); " ;
      
        $this->executeQuery($query);
        
        return  $this->getLastInserId();
    }

    public function setPuntos($idUsuario, $puntos, $isNew)
    {
        $query="";
        if ($isNew) {
            $query = "INSERT INTO cartera
            (         
              puntosdisponibles,
              idusuario
            )
            VALUES
            (            
              {$puntos},
              {$idUsuario}
            );";
        } else {
            $query = "UPDATE  cartera  SET         
            puntosdisponibles = {$puntos}
            where idusuario = {$idUsuario}";
        }
       
        
        if ($this->executeQuery($query)) {
            return true;
        } else {
            return false;
        }
    }


    public function setAbonoCartera($idReferenciaPago, $idUsuario, $monto, $idCartera)
    {
        $query = "INSERT INTO abonocartera (idreferenciapago,fechaabono,montoingresado,idcartera) 
                VALUES('{$idReferenciaPago}', curdate(), {$monto}, {$idCartera} )  ";
           
        if ($this->executeQuery($query)) {
            return true;
        } else {
            return false;
        }
    }

    public function getAbonoByIdReferencia($idReferenciaPago)
    {
        $query = "SELECT idcartera, montoingresado FROM abonocartera WHERE idreferenciapago = '{$idReferenciaPago}' ";
        return $this->getAll($query);
    }

    public function sumarPuntosCartera($puntos, $idCartera)
    {
        $query = "UPDATE cartera  SET puntosdisponibles = puntosdisponibles + {$puntos} WHERE idcartera = {$idCartera}  ";
        
        return $this->executeQuery($query);
    }

    public function getAbonos($idUsuario)
    {
        $query = "SELECT oc.idordercompra, oc.estatus as estatusordencompra, oc.totalcompra,oc.idevento, oc.fechaalta, e.nombreevento,oc.numboletoscompra 
        FROM ordencompra oc INNER JOIN eventos e on oc.idevento = e.idevento 
        WHERE oc.estatus =10 AND oc.idusuario = {$idUsuario}"; 
        
     
        return $this->getAll($query);
    }
    public function getAbonosPago($idOrdenCompra)
    {
        $query = "SELECT * FROM abonospago WHERE idordercompra={$idOrdenCompra} AND estatus = 1";
        
        return $this->getAll($query);
    }


    public function agregarAbonoPago($idOrdenCompra, $idReferenciaPago, $monto, $estatus)
    {
        
        $query = "INSERT INTO abonospago (idreferenciapago, fechapago, montopagado, estatus, idordercompra) 
        VALUES('{$idReferenciaPago}', curdate(), {$monto}, {$estatus}, {$idOrdenCompra} )  ";
    
       
        if ($this->executeQuery($query)) {
            return true;
        } else {
            return false;
        }
    }

    public function actualizarEstatusAbonoPago( $idReferenciaPago, $estatus)
    {
        $query = "UPDATE abonospago  set estatus =  {$estatus} WHERE idreferenciapago='{$idReferenciaPago}' ";
   
        return $this->executeQuery($query);
    }
}
