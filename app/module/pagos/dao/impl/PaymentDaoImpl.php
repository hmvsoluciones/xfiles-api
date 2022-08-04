<?php

class PaymentDaoImpl extends Connection implements PaymentDao {
    public function loadOrdenCompra($params){
       $usuario= $params['usuario'];
       $rp = $params["rp"];
       $idReferenciaPago =$params['idreferenciapago'];
        
       if($idReferenciaPago==null){
        $idReferenciaPago ="null";
       }
       if($usuario==null){
        $usuario ="null";
       }
       if($rp == null || $rp == ""){
            $rp ="null";
       }
       if(!isset($params['puntos'])){
        $params['puntos'] = 0;
       }
       
        $query = "INSERT INTO ordencompra
        (         
          numboletoscompra,
          totalcompra,
          idusuario,
          metodopago,
          idevento,
          idreferenciapago,
          idusualta,
          fechaalta,          
          estatus,
          rp,
          puntos
        )
        VALUES
        (          
            {$params['numboletoscompra']},
            {$params['totalcompra']},
            {$usuario},
            '{$params['metodopago']}',
            {$params['evento']},
            '{$idReferenciaPago}',            
            {$usuario},
            now(),                        
            {$params['estatus']},
            {$rp},
            {$params['puntos']}
        );";               
      
        if ($this->executeQuery($query)) {            
            return $this->getLastInserId();
        } else {
            return FALSE;
        }
        
    }

    public function updateIdReferenciaOrdenCompra($params ){
        
        $idReferenciaPago= $params['idreferenciapago'];
        $idordercompra = $params['idordercompra'];
        
        $query = "UPDATE  ordencompra  SET idreferenciapago = '{$idReferenciaPago}' WHERE  idordercompra= {$idordercompra} ";
       
          return $this->executeQuery($query);
         
     }

     public function updateIdReferenciaAndMethodOrdenCompra($params ){
        
        $idReferenciaPago= $params['idreferenciapago'];
        $idordercompra = $params['idordercompra'];
        $metodopago = $params['metodopago'];
        $estatus = $params['estatus'];

        $query = "UPDATE  ordencompra  SET idreferenciapago = '{$idReferenciaPago}', metodopago ='{$metodopago}',  estatus = {$estatus}
         WHERE  idordercompra= {$idordercompra} ";

       
          return $this->executeQuery($query);
         
     }

    public function updateEstatusOrdenCompra($params ){
        
        $idReferenciaPago= $params['idReferenciaPago'];
        $estatus = $params['estatus'];
        
        $query = "UPDATE  ordencompra  SET estatus = {$estatus} WHERE  idreferenciapago= '{$idReferenciaPago}' ";
         
          return $this->executeQuery($query);
         
     }

     public function updateEstatusOrdenCompraPagadaFromAbonos($idOrdenCompra ){
        
        
        
        $query = "UPDATE  ordencompra  SET estatus = 11 WHERE  idordercompra = {$idOrdenCompra} ";
         
          return $this->executeQuery($query);
         
     }

     public function setBoletosPagadosByIdOrdenCompra($idOrdenCompra) {
        $query = "UPDATE kardexboletos kb
            SET kb.estatus = 1
            WHERE kb.idkardexboletos IN (SELECT doc.idkardexboletos
                                  FROM detalleordencompra doc
                                    INNER JOIN ordencompra oc
                                            ON oc.idordercompra = doc.idordercompra
                                           AND oc.idordercompra = {$idOrdenCompra});";
        if ($this->executeQuery($query)) {            
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function loadDetalleOrdenCompra($params){
        $query = "INSERT INTO detalleordencompra
        (         
          idordercompra,
          idkardexboletos
        )
        VALUES
        (          
          {$params["idordercompra"]},
          {$params["idkardexboletos"]}
        );";

        if ($this->executeQuery($query)) {            
            return $this->getLastInserId();
        } else {
            return FALSE;
        }
        
    }
    public function setBoletosPagadosByIdReferencia($idReferenciaPago) {
        $query = "UPDATE kardexboletos kb
            SET kb.estatus = 1
            WHERE kb.idkardexboletos IN (SELECT doc.idkardexboletos
                                  FROM detalleordencompra doc
                                    INNER JOIN ordencompra oc
                                            ON oc.idordercompra = doc.idordercompra
                                           AND oc.idreferenciapago = '{$idReferenciaPago}');";
        if ($this->executeQuery($query)) {            
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function setBoletosVencidosByIdReferencia($idReferenciaPago) {
        $query = "UPDATE kardexboletos kb
            SET kb.estatus = -1
            WHERE kb.idkardexboletos IN (SELECT doc.idkardexboletos
                                  FROM detalleordencompra doc
                                    INNER JOIN ordencompra oc
                                            ON oc.idordercompra = doc.idordercompra
                                           AND oc.idreferenciapago = '{$idReferenciaPago}');";
        if ($this->executeQuery($query)) {            
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getIdReferenciaOfOrdenCompra($idOrdenCompra){
        $query = "SELECT idreferenciapago FROM ordencompra WHERE idordercompra={$idOrdenCompra}";

        return $this->getAll($query);
    }

    public function getIdOdenCompraFromIdReferenciaPago($idReferenciaPago){
        $query = "SELECT idordercompra  FROM ordencompra WHERE idreferenciapago = '{$idReferenciaPago}'";

        return $this->getAll($query);
    }

    public function getOrdenCompra($idOrdenCompra){
        $query = "SELECT o.idordercompra, o.numboletoscompra, o.totalcompra , o.idusuario, o.idevento, o.idreferenciapago, o.fechaalta, o.estatus, o.metodopago 
        FROM ordencompra o
        WHERE o.idordercompra = {$idOrdenCompra}";
       
        return $this->getAll($query);
    }



    public function retrieveDetalleDeOrdenCompra($idOrdenCompra){
        $query = "SELECT t.tipoboleto, t.idtipoboleto , t.precioboleto, oc.iddescuentoevento, de.porcentajemontodescuento, u.porcentajecomision, o.puntos  FROM kardexboletos k 
            JOIN detalleordencompra d on d.idkardexboletos = k.idkardexboletos 
            JOIN ordencompra o  on o.idordercompra  = d.idordercompra 
            JOIN eventos e on e.idevento = o.idevento 
            JOIN usuarios u on u.idusuario = e.idusuario 
            JOIN tipoboleto t on k.idtipoboleto = t.idtipoboleto 
            LEFT JOIN ordencompradescuentos oc on oc.idordercompra = o.idordercompra 
            LEFT JOIN descuentosevento de on de.iddescuentoevento  = oc.iddescuentoevento 
            WHERE o.idordercompra = {$idOrdenCompra}";

            return $this->getAll($query);

    }

    public function updateDatosUsuarioInKardexAndEstatus($params){
        $query = "UPDATE kardexboletos SET nombrepersona = '{$params["cliente"]["nombre"]}', 
                    correopersona = '{$params["cliente"]["correo"]}', 
                    telefonopersona = '{$params["cliente"]["celular"]}',
                    estatus = {$params['estatus']}
                WHERE idkardexboletos in (SELECT idkardexboletos FROM detalleordencompra doc WHERE doc.idordercompra = {$params["idordercompra"]})";
        
        return $this->executeQuery($query);       
        
}

    public function saveOrdenCompraDescuento($descuentoId, $idOrdenCompra){
        $query = "INSERT INTO ordencompradescuentos
        (         
            iddescuentoevento,
            idordercompra
        )
        VALUES
        (          
          {$descuentoId},
          {$idOrdenCompra}
        );";

        if ($this->executeQuery($query)) {            
            return $this->getLastInserId();
        } else {
            return FALSE;
        }
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
}
