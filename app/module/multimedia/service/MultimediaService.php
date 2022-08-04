<?php

interface MultimediaService {
    /**
     * Creacion de funcion de negocio para agregar registro en multimedia y dropbox
     * 
     * @param Object [multimedia] objeto valores
     *  idRemote
     *  nombreMultimedia
     *  urlMultimedia
     *  urlMultimediaGet
     *  extensionMultimedia
     * @return Object WSResponse
     */
    public function add($multimedia);     
    
    /**
     * Creacion de funcion de negocio para agregar registro en multimedia y dropbox
     * 
     * @param Object [multimedia] objeto valores
     *  idRemote
     *  nombreMultimedia
     *  urlMultimedia
     *  urlMultimediaGet
     *  extensionMultimedia
     * @return Object Multimedia
     */
    public function addMultiple($multimedia);  

    /**
     * Creacion de funcion de negocio para eliminar registro de drombox por id
     * 
     * @param Integer [idRemote] id de dropbox
     * @return Object WSResponse
     */
    public function delete($idRemote);

    /**
     * Creacion de funcion de negocio para obtener registro por id
     * 
     * @param Integer [id] id de la tabla demo
     * @return Object WSResponse
     */
    public function get($id);
}
