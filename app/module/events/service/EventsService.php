<?php

interface EventsService {
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
     * Creacion de funcion de negocio para obtener registro por id
     * 
     * @param Integer [id] id de la tabla demo
     * @return Object WSResponse
     */
    public function get($id);

    public function getEventArray($id);
    /**
     * Creacion de funcion de negocio para agregar registro
     *      
     * @return Object WSResponse
     */
    public function getAllData();

    /**
     * Creacion de funcion de negocio para agregar registro
     *      
     * @return Object WSResponse
     */
    public function getAllEventosByIdUser($id);
    /**
     * Creacion de funcion de negocio para eliminar registro por id
     * 
     * @param Integer [id] id de la tabla demo
     * @return Object WSResponse
     */
    public function delete($id);
    
    //MensajesEventoDTO    
    public function addMensajeEvento($mensaje);        
    public function updateMensajeEvento($mensaje);   
    public function getMensajeEvento($idMensajeEvento);
    public function getAllMensajesEventoByEvento($idEvento);
    public function deleteMensajesEvento($idMensajeEvento);
    
    //RedesSocialesEventoDTO    
    public function addRedesSociales($redSocial);
    public function addRedesSocialesAll($redesSociales);       
    public function updateRedesSociales($redSocial);   
    public function getRedesSociales($idRedSocial);
    public function getAllRedesSocialesByEvento($idEvento);
    public function deleteRedesSociales($idRedSocial);
    
    //DescuentosEventoDTO    
    public function addDescuentosEvento($descuento);        
    public function updateDescuentosEvento($descuento);   
    public function getDescuentosEvento($idDescuento);
    public function getAllDescuentosEventoByEvento($idEvento);
    public function deleteDescuentosEvento($idDescuento);
    
    //UbicacionEventoDTO    
    public function addUbicacionEvento($ubicacion);        
    public function updateUbicacionEvento($ubicacion);   
    public function getUbicacionEvento($idUbicacion);
    public function getAllUbicacionesEventoByEvento($idEvento);
    public function deleteUbicacionEvento($idUbicacion);
    
    //EstructurasEventoDTO    
    public function addEstructurasEvento($estructura);
    public function addEstructurasEventoMultiple($estructura);
    public function updateEstructurasEvento($estructura);
    public function getEstructurasEvento($idEstructura);
    public function getAllEstructurasEventoByEvento($idEvento);
    public function deleteEstructurasEvento($idEstructura);

    //Events Site
    public function getEventSites($filtros, $nombreEvento, $fechaEvento, $idEvento, $sessionUser);    
    public function getEventInfo($event);

    //Tipo boleto
    public function saveTipoBoletoEvento($estructura);
    public function getAllTipoBoletoByEvento($idEvento);
    public function getTipoBoletoById($idTipoBoleto);
    public function deleteTipoBoletoById($id);



    public function addUsuarioAppEvento($usuarioApp);
    public function getAllUsuariosAppByEvento($idEvento);
    public function deleteUsuarioAppById($id);

    public function getEventTicket($event);

    //usuarios relaciones publicas
    //public function addUsuarioAppEvento($usuarioApp);
    public function getAllUsuariosRelPublicByEvento($idEvento);
    //public function existsUsuarioAppEvento($idEvento, $idUsuario);
    //public function deleteUsuarioAppById($id);

    public function getAllEventosByIdUserRP($idUser);

    public function updateStatus($idEvent, $newStatus);

    public function publicarEvento($idEvent);

    public function findAddUrlEvento($url);

    public function findUpdateUrlEvento($url, $idEvento);

    public function getIdEventoByUrlEvento($urlEvento);

    public function findAddUrlEventoRP($url);

    public function saveComplementoURLRP($idRelacionesPublicas, $complementourlrp);

    public function changeStatusTipoBoleto($idTipoBoleto, $newStatus);
}
