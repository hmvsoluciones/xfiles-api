<?php

class OrdenCompraDaoImpl extends Connection implements OrdenCompraDao {

    private $util;

    function __construct(){
        $this->util = new UtilImpl();
    }

    public function add($ordenCompra){
        $query = "INSERT INTO ordencompra(
            idordercompra,
            numboletoscompra,
            totalcompra,
            idusuario,
            idmetodopago,
            idevento,
            idusualta,
            fechaalta,
            idusumodifica,
            fechamodifica,
            estatus
        ) VALUES(
            {$ordenCompra->getIdordercompra()},
            {$ordenCompra->getNumboletoscompra()},
            {$ordenCompra->getTotalcompra()},
            {$ordenCompra->getIdusuario()},
            {$ordenCompra->getIdmetododepago()},
            {$ordenCompra->getIdevento()},
            {$ordenCompra->getIdusualta()},
            '{$ordenCompra->getFechaalta()}',
            {$ordenCompra->getIdusumodifica()},
            '{$ordenCompra->getFechamodifica()}',
            {$ordenCompra->getEstatus()},
            
            )";
        
        if ($this->executeQuery($query)) {            
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function findAbonoByIdOrdenCompra($idOrdenCompra){
        $query = "SELECT * FROM abonospago WHERE idordercompra = {$idOrdenCompra}";        
        
        return $this->getAll($query);           
    }

    public function update($demo){
        $query = "UPDATE demos SET TEXTVALUE ='{$demo->getTextValue()}' WHERE ID = {$demo->getId()}";
        
        if ($this->executeQuery($query)) {            
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get($id){
        $query = "SELECT * FROM demos WHERE ID = {$id}";        
        
        return $this->getAll($query);           
    }

    public function getAllData(){
        $query = "SELECT * FROM demos";        
        
        return $this->getAll($query);           
    }

    public function delete($id){
        $query = "DELETE FROM demos WHERE ID = {$id}";
        
        if ($this->executeDelete($query)) {            
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
