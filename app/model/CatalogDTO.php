<?php

class CatalogDTO {
    private $id;
    private $textValue;
    
    function getId() {
        return $this->id;
    }

    function setId($id) {
        $this->id = $id;
    }

    function getTextValue() {
        return $this->textValue;
    }
    
    function setTextValue($textValue) {
        $this->textValue = $textValue;
    }

    public function expose() {
        return get_object_vars($this);
    }

}