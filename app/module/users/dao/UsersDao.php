<?php

interface UsersDao {

      /**
     * INSERT en tabla demo
     * 
     * @param Object [$obj] objeto valores id y textValue
     * @return boolean Boolean
     */
    public function add($obj);

    /**
     * Agregar estado a usuario del portal
     */
    public function addEstado($idEstado, $idUsuario);
    
    /**
     * Actualización de tabla demo
     * 
     * @param Object [$obj] objeto valores id y textValue
     * @return boolean Boolean
     */
    public function update($obj);

    /**
     * Consulta de registro de tabla demo
     * 
     * @param Integer [id] id de la tabla demo
     * @return Array Objeto por id
     */
    public function get($id);

    /**
     * Bucas por correo a un para Evento RP, Punto de Venta y AppMovil
     * @param String correo
     * @return Array Objeto por id
     */
    public function getOneFromAppMovilRPPuntoVenta($correoUsuario);

    /**
     * Consulta de todos los registros de tabla demo
     *      
     * @return Array Objetos de toda la tabla
     */
    public function getAllData();

    /**
     * Consulta de registro de tabla demo
     * 
     * @param String [email] id de la tabla demo
     * @return Array Objeto por id
     */
    public function getByEmail($email);

    /**
     * Login
     * 
     * @param String [email]
     * @param String [password]
     * @return Array Objeto
     */    
    public function getByEmailPassword($email, $password);

    /**
     * Update credential
     * 
     * @param String [idUser]
     * @param String [newCredential]
     * @return Array Objeto
     */    
    public function updateCredential($idUser, $newCredential);

    /**
     * Password recovery
     *      
     * @param String [email]
     * @return Array Objeto
     */    
    public function passwordRecovery($email);

    /**
     * Password recovery
     *      
     * @param String [email]
     * @return Array Objeto
     */        
    public function getAllDataByIdUser($idUser);

    public function getBuyHistory($idUser);

    public function updateStatus($idUser, $newStatus);

    public function getByEmailNotByIdUsuario($email, $idUsuario);

    public function getSellRPHistory($idEvent, $idUserRP);

    public function addLogoProductor($idMultimedia, $idUsuario);

    public function getLogoProductor($idUsuario);

    public function deleteLogoProductor($idUsuario, $idMultimedia);

    public function getLogoProductorMultimedia($idUsuario);
}
