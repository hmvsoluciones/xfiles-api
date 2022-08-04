<?php

class UsersDaoImpl extends Connection implements UsersDao {

    private $util;

    function __construct(){
        $this->util = new UtilImpl();
    }

    public function add($usuario){
        $randomPassword = $this->randomPassword(8);

        $apUsu =(empty($usuario->getAppsusu()))?"NULL":"'{$usuario->getAppsusu()}'";

        $query = "INSERT INTO usuarios(correousu,contraseniausu,nombreusu,appusu,ampusu,idtipousuario, telefonousu, direccionusu,idusuarioalta, fechaalta, estatus, porcentajecomision) VALUES('{$usuario->getCorreoUsu()}','{$this->util->encrypt($randomPassword)}','{$usuario->getNombreUsu()}', {$apUsu},'{$usuario->getApmsusu()}',{$usuario->getIdTipoUsu()}, '{$usuario->gettelefonoUsu()}', '{$usuario->getdireccionUsu()}', {$usuario->getIdUsuarioAlta()}, now(), 1, {$usuario->getPorcentajeComision()})";

        if ($this->executeQuery($query)) {            
            return array(
                "idUsuario" => $this->getLastInserId(), 
                "password" => $randomPassword
            );
        } else {
            return FALSE;
        }
    }
    public function addEstado($idEstado, $idUsuario){
        $query = "INSERT INTO estadousuarios
                (          
                identidadfederativa,
                idusuario
                )
                VALUES
                (          
                {$idEstado},
                {$idUsuario}
                );";
         if ($this->executeQuery($query)) {
            return TRUE;
         } else {
             return FALSE;
         }
    }

   

    public function update($usuario){
        $query = "UPDATE usuarios SET correousu='{$usuario->getCorreoUsu()}', porcentajecomision ={$usuario->getPorcentajeComision()}, nombreusu ='{$usuario->getNombreUsu()}',appusu='{$usuario->getAppsusu()}',ampusu= '{$usuario->getApmsusu()}', idtipousuario={$usuario->getIdTipoUsu()}, telefonousu= '{$usuario->gettelefonoUsu()}', direccionusu='{$usuario->getdireccionUsu()}', idusumodifica={$usuario->getIdUsuarioModifica()}, fechamodifica = now() WHERE idusuario = {$usuario->getIdUsuario()}";        
        
        if ($this->executeQuery($query)) {            
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get($id){
        $query = "SELECT u.idusuario,
                u.correousu,
                u.contraseniausu,
                u.nombreusu,
                u.appusu,
                u.ampusu,
                u.idtipousuario,
                c.nombre AS tipoUsuDesc,
                u.telefonousu,
                u.direccionusu,
                u.porcentajecomision,
                CONCAT(ua.nombreusu,' ',ua.appusu,' ', ua.ampusu) creado_por
        FROM usuarios u
        INNER JOIN cattipousuario c ON u.idtipousuario = c.id
        LEFT JOIN usuarios ua ON ua.idusuario = u.idusuarioalta
        WHERE u.idusuario = {$id}";               
        
        return $this->getAll($query);           
    }

    public function getOneFromAppMovilRPPuntoVenta($correoUsuario){
        $query = "SELECT u.idusuario, u.correousu,u.contraseniausu,u.nombreusu,u.appusu,u.ampusu,u.idtipousuario,c.nombre AS tipoUsuDesc,u.telefonousu,u.direccionusu FROM usuarios u INNER JOIN cattipousuario c ON u.idtipousuario = c.id WHERE u.correousu = '{$correoUsuario}' AND u.estatus = 1 AND u.idtipousuario IN(3,5,15)";        
        return $this->getAll($query);           
    }

    public function getAllData(){
        $query = "SELECT u.*,
                CONCAT(ua.nombreusu,' ',ua.appusu,' ', ua.ampusu) creado_por,
                c.nombre AS tipoUsuDesc
        FROM usuarios u
        INNER JOIN cattipousuario c ON u.idtipousuario = c.id
        INNER JOIN usuarios ua ON ua.idusuario = u.idusuarioalta";        
        
        return $this->getAll($query);           
    }

    function randomPassword($length) {

        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        return substr(str_shuffle($chars),0,$length);
    
    }   
    
    public function getByEmail($email){
       $query = "SELECT * FROM usuarios WHERE correousu = '{$email}'";       
        
        return $this->getAll($query);           
    }
    public function getByEmailPassword($email, $password){
        $password = $this->util->encrypt($password);
        
        $query = "SELECT u.idusuario, u.correousu,u.contraseniausu,u.nombreusu,u.appusu,u.ampusu,u.idtipousuario,c.nombre AS tipoUsuDesc,u.telefonousu,u.direccionusu, u.porcentajecomision FROM usuarios u INNER JOIN cattipousuario c ON u.idtipousuario = c.id WHERE u.correousu = '{$email}' AND u.contraseniausu='{$password}' AND u.estatus = 1";
         return $this->getAll($query);           
     }

     public function updateCredential($idUsuario, $newCredential){                
         $query = "UPDATE usuarios SET contraseniausu ='{$this->util->encrypt($newCredential)}' WHERE idusuario = {$idUsuario}";
        if ($this->executeQuery($query)) {            
            return TRUE;
        } else {
            return FALSE;
        }
     }
    
     public function passwordRecovery($email){        
        $randomPassword = $this->randomPassword(8);
        $query = "UPDATE usuarios SET contraseniausu ='{$this->util->encrypt($randomPassword)}' WHERE correousu = '{$email}'";
        
        if ($this->executeQuery($query)) {            
            return $randomPassword;
        } else {
            return FALSE;
        }

     }
/**
     * Password recovery
     *      
     * @param String [email]
     * @return Array Objeto
     */        
    public function getAllDataByIdUser($idUser){
        $query = "SELECT u.idusuario,
                    u.correousu,
                    u.contraseniausu,
                    u.nombreusu,
                    u.appusu,
                    u.ampusu,
                    u.idtipousuario,
                    c.nombre AS tipoUsuDesc,
                    u.telefonousu,
                    u.direccionusu,
                    u.porcentajecomision,
                    CONCAT(ua.nombreusu,' ',ua.appusu,' ',ua.ampusu) creado_por
            FROM usuarios u
            INNER JOIN cattipousuario c
                    ON u.idtipousuario = c.id
                    AND u.estatus = 1
                    AND u.idusuarioalta = {$idUser}
            INNER JOIN usuarios ua ON ua.idusuario = u.idusuarioalta";        
        
        return $this->getAll($query);           
    }

    //Reporte de clientes
    public function getAllUserClientes($idUser){
        $query = "SELECT u.idusuario,u.correousu,
        u.nombreusu,
        u.appusu,
        u.ampusu,
        u.idtipousuario,
        u.telefonousu,
        u.direccionusu,
         c.nombre AS tipoUsuDesc,
         c2.nombre as estado 
        FROM usuarios u 
        INNER JOIN cattipousuario c ON u.idtipousuario = c.id
        left join estadousuarios e ON  u.idusuario =e.idusuario 
        left join catentidadfederativa c2 on e.identidadfederativa = c2.clavecat 
        AND u.estatus = 1
        where idtipousuario = 4 ";        
        
        return $this->getAll($query);           
    }


    public function getAllDataRPByIdUser($idUser){
        $query = "SELECT u.idusuario, u.correousu,u.contraseniausu,u.nombreusu,u.appusu,u.ampusu,u.idtipousuario,c.nombre AS tipoUsuDesc,u.telefonousu,u.direccionusu FROM usuarios u INNER JOIN cattipousuario c ON u.idtipousuario =c.id AND u.estatus = 1  where idtipousuario =5";        //AND u.idusuarioalta = {$idUser}
        
        return $this->getAll($query);           
    }

    public function getBuyHistory($idUser){
        $query = "SELECT * FROM ordencompra WHERE idusuario = {$idUser} ORDER BY fechaalta DESC";        
        
        return $this->getAll($query); 
    }

    public function updateStatus($idUser, $newStatus){
        $query = "UPDATE usuarios SET estatus ={$newStatus} WHERE idusuario = {$idUser}";        
        
        if ($this->executeQuery($query)) {            
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function getByEmailNotByIdUsuario($email, $idUsuario){
        $query = "SELECT * FROM usuarios WHERE correousu = '{$email}' AND idusuario <> {$idUsuario}";       
        
        return $this->getAll($query);   
    }
    
    public function getSellRPHistory($idEvent, $idUserRP){
        $query = "select o.idordercompra,o.numboletoscompra,o.totalcompra,o.idusuario,o.idevento,o.idreferenciapago,o.idusualta,o.fechaalta,
        o.idusumodifica,o.fechamodifica,o.estatus,o.metodopago,o.rp,o.puntos, k.correopersona 
       FROM ordencompra o inner join detalleordencompra d ON o.idordercompra =d.idordercompra inner join kardexboletos k on d.idkardexboletos =k.idkardexboletos
       WHERE rp = {$idUserRP} AND idevento={$idEvent} and o.estatus in (1,11) ORDER BY o.fechaalta DESC";        
       return $this->getAll($query);
    }
    public function addLogoProductor($idMultimedia, $idUsuario){
        $query = "INSERT INTO usuariomultimedia
        (          
          idmultimedia,
          idusuario
        )
        VALUES
        (          
          {$idMultimedia},
          {$idUsuario}
        );
        ";
        if ($this->executeQuery($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function getLogoProductor($idUsuario){
        $query = "SELECT m.* FROM multimedia m
        INNER JOIN usuariomultimedia um ON um.idmultimedia = m.idmultimedia
        WHERE idusuario = {$idUsuario}";
        
        return $this->getAll($query);
    }

    public function deleteLogoProductor($idUsuario, $idMultimedia){
        $query = "DELETE FROM usuariomultimedia WHERE idusuario = {$idUsuario} AND idMultimedia = {$idMultimedia}";
        
        if ($this->executeDelete($query)) {            
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function getLogoProductorMultimedia($idUsuario){
        $query = "SELECT m.urlmultimediaget FROM  usuariomultimedia um
        INNER JOIN multimedia m ON m.idmultimedia = um.idmultimedia AND um.idusuario ={$idUsuario}";                
        return $this->getAll($query);
        
    }
}
