<?php

interface UsersService {
    /**
     * Creacion de funcion de negocio para agregar registro
     * 
     * @param Object [$demo] objeto valores id y textValue
     * @return Object WSResponse
     */
    public function add($demo);
    
    /**
     * Creacion de funcion de negocio para actualizar registro
     * 
     * @param Object [$demo] objeto valores id y textValue
     * @return Object WSResponse
     */
    public function update($demo);

       /**
     * Bucas por correo a un usuario de relaciones publicas, punto de venta y app movil
     * 
     * @param String correo
     * @return Array Objeto por id
     */
    public function getOneFromAppMovilRPPuntoVenta($correoUsuario);

    /**
     * Creacion de funcion de negocio para obtener registro por id
     * 
     * @param Integer [id] id de la tabla demo
     * @return Object WSResponse
     */
    public function get($id);

    /**
     * Creacion de funcion de negocio para agregar registro
     *      
     * @return Object WSResponse
     */
    public function getAllData();

     /**
     * Creacion de funcion de negocio para obtener registro por id
     * 
     * @param String [email] id de la tabla demo
     * @return Object WSResponse
     */
    public function getByEmail($email);

    /**
     * Login de aplicaciones
     * 
     * @param String [principal]
     * @param String [credencial]
     * @return Object WSResponse
     */
    public function login($principal, $credencial);

    /**
     * Login de aplicaciones
     * 
     * @param String [principal]
     * @param String [credencial]
     * @return Object WSResponse
     */
    public function loginApp($principal, $credencial);

    /**
     * Modificar password
     *      
     * @param String [credencial]
     * @param String [newCredencial]
     * @param String [confirmCredencial]
     * @return Object WSResponse
     */
    public function updateCredential($idUser, $credencial, $newCredencial, $confirmCredencial);

    /**
     * Modificar password
     *      
     * @param String [newCredencial]
     * @param String [confirmCredencial]
     * @return Object WSResponse
     */
    public function updateCredentialAdmin($idUser, $newCredencial);

    /**
     * Password recovery
     *      
     * @param String [email]        
     * @return Object WSResponse
     */
    public function passwordRecovery($email);

    /**
     * Password recovery
     *      
     * @param String [email]        
     * @return Object WSResponse
     */    
    public function getAllDataByIdUser($idUser);

    public function getBuyHistory($idUser);

    public function updateStatus($idUser, $newStatus);

    public function getByEmailNotByIdUsuario($email, $idUsuario);

    public function getSellRPHistory($idEvent, $idUserRP);

    public function addLogoProductor($idMultimedia, $idUsuario);
    
    public function getLogoProductor($idUsuario);

    public function deleteLogoProductor($idUsuario, $idMultimedia);
}
