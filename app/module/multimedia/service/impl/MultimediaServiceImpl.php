<?php

class MultimediaServiceImpl implements MultimediaService
{

    private $demoDao;

    function __construct()
    {
        $this->multimediaDao = new MultimediaDaoImpl();
    }

    public function add($multimedia)
    {        
        $insert =    $this->multimediaDao->add($multimedia);
        if ($insert) {
            $multimedia->setIdMultimedia($insert);
            return $multimedia;
        } else {
            return FALSE;
        }
    }
    public function addMultiple($multimedia)
    {
        $insert =    $this->multimediaDao->add($multimedia);
        if ($insert) {
            $multimedia->setIdMultimedia($insert);
            return $multimedia->expose();
        } else
            return NULL;
    }


    public function delete($idRemote)
    {
        return $this->multimediaDao->delete($idRemote);
    }

    public function get($id)
    {
        $response = new WSResponse();
        $demo =    $this->multimediaDao->get($id);
        if ($demo && count($demo) > 0) {
            $response->setSuccess(true);
            $response->setMessage("Registro obtenido correctamente");
            $response->setObject($demo[0]);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }
}
