<?php

interface EventsDao {

      /**
     * INSERT en tabla demo
     * 
     * @param Object [$obj] objeto valores id y textValue
     * @return boolean Boolean
     */
    public function add($obj);
    
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
     * Consulta de todos los registros de tabla demo
     *      
     * @return Array Objetos de toda la tabla
     */
    public function getAllData();
   /**
     * Consulta de todos los registros de tabla eventos por ID de USUARIO
     *      
     * @return Array Objetos de toda la tabla
     */
    public function getAllEventosByIdUser($id);
    /**
     * Elimar registro de tabla
     * 
     * @param Integer [id] id de la tabla demo
     * @return Boolean respuesta de ejecución
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
    public function updateRedesSociales($redSocial);
    public function updateRedSocialUrl($redSocial);
    public function exitsRedSocial($idEvento, $idTipoRedSocial);
    public function getRedesSociales($idRedSocial);
    public function getAllRedesSocialesByEvento($idEvento);
    public function deleteRedesSociales($idRedSocial);
    
   


    //DescuentosEventoDTO    
    public function addDescuentosEvento($descuento);        
    public function updateDescuentosEvento($descuento);   
    public function getDescuentosEvento($idDescuento);
    public function getAllDescuentosEventoByEvento($idEvento);
    public function deleteDescuentosEvento($idDescuento);
    
      //ListaInvitados    
      public function addListaInvitados($listaInvitados);        
      public function updateListaInvitatos($listaInvidatos);   
      public function getListaInvitados($idListaInvidatos);
      public function getAllListaInvidatosByEvento($idEvento);
      public function deleteListaInvitados($idListaInvitados);

    //UbicacionEventoDTO    
    public function addUbicacionEvento($ubicacion);        
    public function updateUbicacionEvento($ubicacion);   
    public function getUbicacionEvento($idUbicacion);
    public function getAllUbicacionesEventoByEvento($idEvento);
    public function deleteUbicacionEvento($idUbicacion);
    
    //EstructurasEventoDTO    
    public function addEstructurasEvento($estructura);
    public function updateEstructurasEvento($estructura);
    public function getEstructurasEvento($idEstructura);
    public function getAllEstructurasEventoByEvento($idEvento);
    public function deleteEstructurasEvento($idEstructura);

    // Eventos getAllSiteEvents
    public function getAllSiteEvents($filtros, $nombreEvento, $fechaEvento, $idEvento, $sessionUser);
    public function getEventEstructura($evento);
    public function getEventPatrociandores($evento);

    //tipo boleto
    public function addTipoBoletoEvento($estructura);
    public function updateTipoBoletoEvento($estructura);
    public function getAllTipoBoletoByEvento($idEvento);
    public function getAllActiveTipoBoletoByEvento($idEvento);
    public function getTipoBoletoById($idTipoBoleto);
    public function deleteTipoBoletoById($id);

     //usuarios app
    public function addUsuarioAppEvento($usuarioApp);
    public function getAllUsuariosAppByEvento($idEvento);
    public function existsUsuarioAppEvento($idEvento, $idUsuario);
    public function deleteUsuarioAppById($id);

    //usuarios relaciones publicas
    //public function addUsuarioAppEvento($usuarioApp);
    public function getAllUsuariosRelPublicByEvento($idEvento);
    //public function existsUsuarioAppEvento($idEvento, $idUsuario);
    //public function deleteUsuarioAppById($id);

    public function getAllEventosByIdUserRP($idUser);


  //Boletos vendidos
  public function getBoletosVendidos();

  public function updateStatus($idEvent, $newStatus);

  public function publicarEvento($idEvent);

  public function findAddUrlEvento($url);

  public function findUpdateUrlEvento($url, $idEvento);

  public function getIdEventoByUrlEvento($urlEvento);
  
  public function getIdRPByUrlEvento($urlEvento);

  public function findAddUrlEventoRP($url);

  public function saveComplementoURLRP($idRelacionesPublicas, $complementourlrp);

  public function mensajesEventoConfirmacion($idEvento);

  public function changeStatusTipoBoleto($idTipoBoleto, $newStatus);
}
