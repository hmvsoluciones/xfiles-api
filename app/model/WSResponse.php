<?php

class WSResponse {

    private $success;
    private $message;
    private $object;

    function getSuccess() {
        return $this->success;
    }

    function getMessage() {
        return $this->message;
    }

    function getObject() {
        return $this->object;
    }

    function setSuccess($success) {
        $this->success = $success;
    }

    function setMessage($message) {
        $this->message = $message;
    }

    function setObject($object) {
        $this->object = $object;
    }

    public function expose() {
        return get_object_vars($this);
    }

}
