<?php

class TicketsDaoImpl extends Connection implements TicketsDao {

    private $util;

    function __construct(){
        $this->util = new UtilImpl();
    }

    public function add($demo){
        $query = "INSERT INTO demos(TEXTVALUE) VALUES('{$demo->getTextValue()}')";
        
        if ($this->executeQuery($query)) {            
            return TRUE;
        } else {
            return FALSE;
        }
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
