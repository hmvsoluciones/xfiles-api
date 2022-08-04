<?php

class UsersServiceImpl implements UsersService {

    private $usersDao;
    private $util;
    function __construct() {
        $this->usersDao = new UsersDaoImpl();
        $this->util = new UtilImpl();
    }

    public function add($demo){        
        $response = new WSResponse();
        $insert =    $this->usersDao->add($demo); 
        
        if(!empty($demo->getEstado())){
            $this->usersDao->addEstado($demo->getEstado(), $insert["idUsuario"]); 
        }
        if($insert){
            $response->setSuccess(true);
            $response->setMessage("Registro guardado correctamente");
            $response->setObject(null);

            $valid=date('Y-m-d H:i:s', strtotime("+2 day"));

            $enlace= URL_BASE."setpassword.php?old={$insert["password"]}&register={$insert["idUsuario"]}&valid={$valid}";

            $params["subject"] = "Registro ticketapp";
            $params["from"] = "servicio@ticketapp.com";
            $params["fromName"] = "Ticket App";
            $params["toArray"] = $demo->getCorreoUsu();
            $params["body"] = "Se confirma su registro de ticketapp con el usuario: <b>{$demo->getCorreoUsu()}</b> y contrase&ntilde;a: <b>{$insert["password"]}</b> <br><br> Opcionalmente puede entrar al siguiente enlace <a href='{$enlace}'> Actualizar password </a> para establecer una contraseña personal, dicho enlace solo estará disponible en las siguientes 24 horas y solo podrá ser usado una vez";
            $this->util->sendMail($params);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al realizar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }
    
    public function update($demo){
        $response = new WSResponse();
        $update =    $this->usersDao->update($demo);        
        if($update){
            $response->setSuccess(true);
            $response->setMessage("Registro actualizado correctamente");
            $response->setObject(null);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al actualizar el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function get($id){        
        $response = new WSResponse();
        $usuario =    $this->usersDao->get($id);        
        if($usuario && count($usuario) > 0){
            $response->setSuccess(true);
            $response->setMessage("Registro obtenido correctamente");
            $response->setObject($usuario[0]);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function getAllData(){        
        $response = new WSResponse();
        $usuario =    $this->usersDao->getAllData();
        if($usuario){
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($usuario);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function getByEmail($email){        
        $response = new WSResponse();
        $user =    $this->usersDao->getByEmail($email);        
        if($user && count($user) > 0){
            $response->setSuccess(true);
            $response->setMessage("Registro obtenido correctamente");
            $response->setObject($user[0]);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

       /**
     * Bucas por correo a un usuario de relaciones publicas
     * 
     * @param String correo
     * @return Array Objeto por id
     */
    public function getOneFromAppMovilRPPuntoVenta($correoUsuario){
        $response = new WSResponse();
        $user =    $this->usersDao->getOneFromAppMovilRPPuntoVenta($correoUsuario);        
        if($user && count($user) > 0){
            $response->setSuccess(true);
            $response->setMessage("Registro obtenido correctamente");
            $response->setObject($user[0]);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No hay registros con ese correo u ocurrio un error");
            $response->setObject(null);
        }

        return $response->expose();
    }
   
    public function loginApp($principal, $credencial){        
        $response = new WSResponse();
        $user =    $this->usersDao->getByEmailPassword($principal, $credencial);        
        if($user && count($user) > 0 
        && ($user[0]["tipoUsuDesc"] == "PUNTO DE VENTA"
        || $user[0]["tipoUsuDesc"] == "APPMOVIL"
        )
        ){
            $response->setSuccess(true);
            $response->setMessage("Bienvenido a ticketapp");
                            
            $responseObj = array("token" => API_KEYS[0],
                                "user" => 
                                    array(
                                        "id" => $user[0]["idusuario"],
                                        "nombre" => $user[0]["nombreusu"],
                                        "app" => $user[0]["appusu"],
                                        "apm" => $user[0]["ampusu"],
                                        "tipo" => $user[0]["idtipousuario"],
                                        "tipoDesc" => $user[0]["tipoUsuDesc"]
                                    )
                                );
            $response->setObject($responseObj);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Usuario no valido, favor de verificar");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function login($principal, $credencial){        
        $response = new WSResponse();
        $user =    $this->usersDao->getByEmailPassword($principal, $credencial);        
        if($user && count($user) > 0){
            $response->setSuccess(true);
            $response->setMessage("Registro obtenido correctamente");
                            
            $responseObj = array("token" => API_KEYS[0],
                                "user" => 
                                    array(
                                        "id" => $user[0]["idusuario"],
                                        "nombre" => $user[0]["nombreusu"],
                                        "app" => $user[0]["appusu"],
                                        "apm" => $user[0]["ampusu"],
                                        "tipo" => $user[0]["idtipousuario"],
                                        "tipoDesc" => $user[0]["tipoUsuDesc"],
                                        "telefono" => $user[0]["telefonousu"],
                                        "correo" => $user[0]["correousu"]

                                    )
                                );
            $response->setObject($responseObj);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function updateCredential($idUser, $credencial, $newCredential, $confirmCredencial){
        $response = new WSResponse();
        $user = $this->usersDao->get($idUser)[0];
        
        if($user["contraseniausu"] != $this->util->encrypt($newCredential)){

            if($user["contraseniausu"] == $this->util->encrypt($credencial)){
                if($newCredential == $confirmCredencial){
                    $update = $this->usersDao->updateCredential($idUser, $newCredential);
                    if($update){
                        $response->setSuccess(true);
                        $response->setMessage("Password actualizado correctamente");
                        $response->setObject(null);                    
                    } else {
                        $response->setSuccess(false);
                        $response->setMessage("Ocurrio un error al actualizar el password");
                        $response->setObject(null);
                    }
                } else {
                    $response->setSuccess(false);
                    $response->setMessage("Los passwords no coinciden, favor de verificar");
                    $response->setObject(null);
                }
            } else {
                    $response->setSuccess(false);
                    $response->setMessage("La contraseña actual no es correcta");
                    $response->setObject(null);
            }
        } else {
            $response->setSuccess(false);
            $response->setMessage("La contraseña nueva no puede ser igual a la contraseña actual");
            $response->setObject(null);
        }        

        return $response->expose();
    }
    public function updateCredentialAdmin($idUser, $newCredential){
        $response = new WSResponse();                        
        $update = $this->usersDao->updateCredential($idUser, $newCredential);
        if($update){
            $response->setSuccess(true);
            $response->setMessage("Password actualizado correctamente");
            $response->setObject(null);                    
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al actualizar el password");
            $response->setObject(null);
        }                         

        return $response->expose();
    }

    public function passwordRecovery($email){
        $response = new WSResponse();        
        if($this->usersDao->getByEmail($email)){
        $newPassword = $this->usersDao->passwordRecovery($email);        
        if($newPassword){
            $response->setSuccess(true);
            $response->setMessage("Se envio el password actualizado al correo: {$email}");
            $response->setObject(null);
            
            $params["subject"] = "Recuperacion de password TicketAppp";
            $params["from"] = "servicio@ticketapp.com";
            $params["fromName"] = "Ticket App";
            $params["toArray"] = $email;
            $params["body"] = "Recuperacion de password el password generado es: <b>{$newPassword}</b><br /> <b>Se recomienda lo actualice al iniciar sesi&oacute;n</b>";
            $this->util->sendMail($params);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible recuperar el password, favor de intentar nuevamente");
            $response->setObject(null);
        }
    } else {
        $response->setSuccess(false);
        $response->setMessage("El usuario no existe, favor de verificar");
        $response->setObject(null);
    }

        return $response->expose();
    }
    public function getAllDataByIdUser($idUser){
        $response = new WSResponse();
        $usuario =    $this->usersDao->getAllDataByIdUser($idUser);
        if($usuario){
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($usuario);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }

    // Reporte de clientes

    public function getAllUserClientes($idUser){
        $response = new WSResponse();
        $usuario =    $this->usersDao->getAllUserClientes($idUser);
        if($usuario){
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($usuario);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function getAllDataRPByIdUser($idUser){
        $response = new WSResponse();
        $usuario =    $this->usersDao->getAllDataRPByIdUser($idUser);
        if($usuario){
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($usuario);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }
    public function getBuyHistory($idUser){
        $response = new WSResponse();
        $responseData =    $this->usersDao->getBuyHistory($idUser);
        $responseHistory = array();

        foreach ($responseData as $key => $value) {
            
            $refPago = $value["idreferenciapago"];
            $key = $this->util->encrypt("compra-0-{$refPago}");

            $item = array(
                "idordercompra" => $value["idordercompra"],
                "numboletoscompra" => $value["numboletoscompra"],
                "totalcompra" => $value["totalcompra"],
                "idusuario" => $value["idusuario"],
                "idevento" => $value["idevento"],
                "idreferenciapago" => $value["idreferenciapago"],
                "idusualta" => $value["idusualta"],
                "fechaalta" => $value["fechaalta"],
                "idusumodifica" => $value["idusumodifica"],
                "fechamodifica" => $value["fechamodifica"],
                "estatus" => $value["estatus"],
                "metodopago" => $value["metodopago"],
                "rp" => $value["rp"],
                "urlboletos" => URL_BASE."document/boletos/?key={$key}"
            );

            array_push($responseHistory, $item);
        }

        if($responseHistory){
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($responseHistory);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }
    public function updateStatus($idUser, $newStatus){
        $response = new WSResponse();                        
        $update = $this->usersDao->updateStatus($idUser, $newStatus);
        if($update){
            $response->setSuccess(true);
            $response->setMessage("Estatus modifcado correctamente");
            $response->setObject(null);                    
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al actualizar el estatus");
            $response->setObject(null);
        }                         

        return $response->expose();
    }
    public function getByEmailNotByIdUsuario($email, $idUsuario){        
        $response = new WSResponse();
        $user =    $this->usersDao->getByEmailNotByIdUsuario($email, $idUsuario);        
        if($user && count($user) > 0){
            $response->setSuccess(true);
            $response->setMessage("Registro obtenido correctamente");
            $response->setObject($user[0]);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }
    public function getSellRPHistory($idEvent, $idUserRP){
        $response = new WSResponse();
        $responseData =    $this->usersDao->getSellRPHistory($idEvent, $idUserRP);
        $responseHistory = array();

        foreach ($responseData as $key => $value) {
            
            $refPago = $value["idreferenciapago"];
            $key = $this->util->encrypt("compra-0-{$value["idordercompra"]}");

            $item = array(
                "idordercompra" => $value["idordercompra"],
                "numboletoscompra" => $value["numboletoscompra"],
                "totalcompra" => $value["totalcompra"],
                "idusuario" => $value["idusuario"],
                "idevento" => $value["idevento"],
                "idreferenciapago" => $value["idreferenciapago"],
                "idusualta" => $value["idusualta"],
                "fechaalta" => $value["fechaalta"],
                "idusumodifica" => $value["idusumodifica"],
                "fechamodifica" => $value["fechamodifica"],
                "estatus" => $value["estatus"],
                "metodopago" => $value["metodopago"],
                "rp" => $value["rp"],
                "correopersona" => $value["correopersona"],
                "urlboletos" => URL_BASE."document/boletos/?key={$key}"

            );

            array_push($responseHistory, $item);
        }

        if($responseHistory){
            $response->setSuccess(true);
            $response->setMessage("Registros obtenido correctamente");
            $response->setObject($responseHistory);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }
    public function addLogoProductor($idMultimedia, $idUsuario) {
        $response = new WSResponse();
        $update =    $this->usersDao->addLogoProductor($idMultimedia, $idUsuario);
        if ($update) {
            $response->setSuccess(true);
            $response->setMessage("Registro creado correctamente");
            $response->setObject(null);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al crear el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }
    public function getLogoProductor($idUsuario){
        $response = new WSResponse();
        $data =    $this->usersDao->getLogoProductor($idUsuario);
        if ($data && count($data) > 0) {
            $response->setSuccess(true);
            $response->setMessage("Registro obtenido correctamente");
            $response->setObject($data);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al obtener el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }
    public function deleteLogoProductor($idUsuario, $idMultimedia){
        $response = new WSResponse();
        $delete =    $this->usersDao->deleteLogoProductor($idUsuario, $idMultimedia);
        if ($delete) {
            $response->setSuccess(true);
            $response->setMessage("Registro obtenido correctamente");
            $response->setObject(null);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al obtener el registro");
            $response->setObject(null);
        }

        return $response->expose();
    }
}