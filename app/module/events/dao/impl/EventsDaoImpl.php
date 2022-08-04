<?php

class EventsDaoImpl extends Connection implements EventsDao {

    private $util;

    function __construct() {
        $this->util = new UtilImpl();
    }

    //Eventos Principal 
    public function add($evento) {
        $decripcionHTML = htmlentities($evento->getDescripcionEvento());
        $query = "INSERT INTO eventos( "
                . " nombreevento, "
                . " urlevento, "
                . " descripcionevento,"
                . " videopromocional,"
                . " idusuario,"
                . " idcategoriaevento,"
                . " idtipoevento,"
                . " ocupacion,"
                . " restricciones,"
                . " fechainicio,"
                . " fechafin,"
                . " horainicio,"
                . " horafin,"
                . " horafinboletogratis,"
                . " idusualta,"
                . " fechaalta,"
                . " estatus"
                . " )"
                . " VALUES("
                . " '{$evento->getNombreEvento()}', "
                . " '{$evento->getUrlEvento()}', "
                . " '{$decripcionHTML}',"
                . " '{$evento->getVideoPromocional()}',"
                . "  {$evento->getIdUsuario()},"
                . "  {$evento->getIdCategoriaEvento()},"
                . "  {$evento->getIdTipoEvento()},"
                . "  {$evento->getOcupacion()},"
                . "  {$evento->getRestricciones()},"
                . " '{$evento->getFechaInicio()}',"
                . " '{$evento->getFechaFin()}',"
                . " '{$evento->getHoraInicio()}',"
                . " '{$evento->getHoraFin()}',"
                . " '{$evento->getHoraFinboleto()}',"
                . " {$evento->getIdUsuarioAlta()},"
                . " now(),"
                . " 1"
                . " )";          
        if ($this->executeQuery($query)) {
            return $this->getLastInserId();
        } else {
            return FALSE;
        }
    }

    public function update($evento) {
        $query = "UPDATE eventos SET "
                . " nombreevento ='{$evento->getNombreEvento()}', "
                . " urlevento ='{$evento->getUrlEvento()}', "
                . " descripcionevento ='{$evento->getDescripcionEvento()}', "
                . " videopromocional ='{$evento->getVideoPromocional()}', "
                . " idcategoriaevento ={$evento->getIdCategoriaEvento()},"
                . " idtipoevento ={$evento->getIdTipoEvento()},"
                . " ocupacion ={$evento->getOcupacion()},"
                . " restricciones ={$evento->getRestricciones()},"
                . " fechainicio ='{$evento->getFechaInicio()}', "
                . " fechafin ='{$evento->getFechaFin()}', "
                . " horainicio ='{$evento->getHoraInicio()}', "
                . " horafin ='{$evento->getHoraFin()}', "
                . " horafinboletogratis ='{$evento->getHoraFinBoleto()}', "
                . " idusumodifica={$evento->getIdUsuarioModifica()}, "
                . " fechamodifica=now() "
                . " WHERE idevento = {$evento->getIdEvento()}";
        
        if ($this->executeQuery($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get($id) {
        $query = "SELECT  * FROM eventos WHERE idevento = {$id}";

        return $this->getAll($query);
    }

    public function getAllData() {
        $query = "SELECT  e.idevento,  e.nombreevento, e.urlevento, e.descripcionevento, e.videopromocional, u.correousu, u.nombreusu, u.appusu, u.ampusu, e.estatus,"
        . " e.fechainicio, e.fechafin, e.horainicio, e.horafin,e.horafinboletogratis ,e.fechaalta, e.idcategoriaevento, e.idtipoevento,"
        . " cce.nombre categoriaevento, cte.nombre tipoevento, e.ocupacion, case when e.restricciones= 1 then 'General (Cualquier edad)' else 'Mayores de edad (+ 18 años)' end restricciones FROM eventos "
        . " e INNER JOIN usuarios u ON e.idusuario = u.idusuario"
        . " INNER JOIN catcategoriaevento cce ON e.idcategoriaevento = cce.id "
        . " INNER JOIN cattipoevento cte ON e.idtipoevento = cte.id ";

        return $this->getAll($query);
    }

    public function getAllSiteEvents($filtros, $nombreEvento, $fechaEvento, $idEvento, $sessionUser) {                
        $where = " WHERE TRUE ";
        if($nombreEvento != null && strlen($nombreEvento) > 2){            
            $where .= " AND UPPER(e.nombreevento) LIKE UPPER('{$nombreEvento}%')";
        }
        if($idEvento != null && !empty($idEvento)){            
            $where .= " AND e.idevento = {$idEvento}";
        }
        if($fechaEvento != null){            
            $where .= " AND e.fechainicio LIKE '{$fechaEvento}'";
        }
        
        // Fitro de favoritos
        if(in_array(-1, $filtros) && !empty($sessionUser)){                       
            $where .= " AND e.idevento IN(SELECT ule.idEvento FROM usuarioslikeevento ule WHERE ule.idusuario = {$sessionUser})";
        }
        // eliminar -1 de favoritos de filtros
        if (($key = array_search(-1, $filtros)) !== false) {
            unset($filtros[$key]);
        }

        if($filtros != null){
            $params = implode(",", $filtros);
            $where .= "AND e.idcategoriaevento IN({$params})";
        }

        if(empty($sessionUser)){
            $sessionUser = "null";
        }
        $query = "SELECT e.*, m.urlmultimediaget as imagenprincipal, (SELECT count(ule.idEvento) FROM usuarioslikeevento ule WHERE ule.idevento = e.idevento) likes, (SELECT count(ule.idEvento) FROM usuarioslikeevento ule WHERE ule.idevento = e.idevento AND ule.idusuario = {$sessionUser}) isliked FROM eventos e"
            ." INNER JOIN eventoestructuras ee ON ee.idevento = e.idevento AND e.idtipoevento IN(45, 55, 56) AND e.estatus = 1 AND e.fechafin > curdate() "
            ." INNER JOIN cattipoelemento cte ON cte.id = ee.idtipoelemento AND cte.clavecat = 6 "
            ." INNER JOIN multimedia m ON m.idmultimedia = ee.idmultimedia {$where} order by e.fechainicio";

        return $this->getAll($query);
    }

    public function getAllEventosByIdUser($idUser) {
        $query = "SELECT  e.idevento,  e.nombreevento,  e.urlevento, e.descripcionevento, e.videopromocional, u.correousu, u.nombreusu, u.appusu, u.ampusu, "
                . " e.fechainicio, e.fechafin, e.horainicio, e.horafinboletogratis, e.horafin, e.fechaalta, e.idcategoriaevento,"
                . " cce.nombre categoriaevento FROM eventos "
                . " e INNER JOIN usuarios u ON e.idusuario = u.idusuario "
                . " INNER JOIN catcategoriaevento cce ON e.idcategoriaevento = cce.id "
                . " AND e.estatus = 1  AND u.idusuario = {$idUser}";

        return $this->getAll($query);
    }

    public function getAllEventosByIdUserRP($idUser) {
        $query = "SELECT  rp.idrelacionespublicas, rp.rpcomplementourl, e.idevento, e.urlevento, e.nombreevento,  e.descripcionevento, e.videopromocional, u.correousu, u.nombreusu, u.appusu, u.ampusu, 
        e.fechainicio, e.fechafin, e.horainicio, e.horafin,e.horafinboletogratis, e.fechaalta, e.idcategoriaevento,
         cce.nombre categoriaevento FROM eventos 
         e INNER JOIN usuarios u ON e.idusuario = u.idusuario 
         INNER JOIN catcategoriaevento cce ON e.idcategoriaevento = cce.id 
         INNER JOIN relacionespublicas rp ON rp.idevento = e.idevento 
         AND rp.idusuario = {$idUser}";

        return $this->getAll($query);
    }

    public function delete($id) {
        $query = "DELETE FROM eventos WHERE idevento = {$id}";

        if ($this->executeDelete($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    //Cierre eventos principal 
    //mensajes eventos 
    public function addMensajeEvento($mensajesEvento) {
         $query = "INSERT INTO mensajeseventoconfirmacion(sms,correo,notas,idevento,idusualta,fechaalta,estatus)"
                . " VALUES('{$mensajesEvento->getSms()}', '{$mensajesEvento->getCorreo()}', '{$mensajesEvento->getNotas()}',{$mensajesEvento->getIdEvento()},{$mensajesEvento->getIdUsuarioAlta()},now(),  1)";
        
        if ($this->executeQuery($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function updateMensajeEvento($mensajesEvento) {
        $query = "UPDATE mensajeseventoconfirmacion SET "
                . " sms ='{$mensajesEvento->getSms()}',"
                . " correo ='{$mensajesEvento->getCorreo()}', "
                . " notas ='{$mensajesEvento->getNotas()}',"
                . " idusumodifica={$mensajesEvento->getIdUsuarioModifica()}, "
                . " fechamodifica=now() "
                . " WHERE idmensajeevento = {$mensajesEvento->getIdMensajeEvento()}";
        

        if ($this->executeQuery($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getMensajesEventoByIdEvento($id) {
        $query = "SELECT * FROM mensajesevento WHERE idevento = {$id}";

        return $this->getAll($query);
    }

    public function getAllDataFromMensajeEvento() {
        $query = "SELECT * FROM mensajesevento";

        return $this->getAll($query);
    }

    public function deleteMensajeEvento($id) {
        $query = "DELETE FROM mensajesevento WHERE idmensajeevento = {$id}";

        if ($this->executeDelete($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function addDescuentosEvento($descuento) {
       $query = " INSERT INTO descuentosevento "
                . " (  "
                . "   nombredescuentoevento,"
                . "   condicionesaplicaciondescuento,"
                . "   porcentajemontodescuento,"
                . "   clavecupon,"
                . "   idevento,"
                . "   idusualta,"
                . "   fechaalta,"
                . "   estatus"
                . " )"
                . " VALUES"
                . " (  "
                . "   '{$descuento->getNombreDescuento()}',"
                . "   '{$descuento->getCondicionesAplicacionDescuento()}',"
                . "   {$descuento->getPorcentajeMontoDescuento()},"
                . "   '{$descuento->getClaveCupon()}',"
                . "   {$descuento->getIdEvento()},"
                . "   {$descuento->getIdUsuarioAlta()},"
                . "   now(),"
                . "   1"
                . " )";
           

        if ($this->executeQuery($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function addEstructurasEvento($estructura) {
        $query = "INSERT INTO eventoestructuras( "
                . " idtipoelemento, "
                . " idmultimedia,"
                . " idevento,"
                . " idusualta,"
                . " fechaalta,"
                . " estatus"
                . " )"
                . " VALUES("
                . " {$estructura->getIdTipoElemento()}, "
                . " {$estructura->getIdMultimedia()},"
                . " {$estructura->getIdEvento()},"
                . "  {$estructura->getIdUsuarioAlta()},"
                . "  now(),"
                . "     1"
                . " )";

        if ($this->executeQuery($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function addRedesSociales($redSocial) {
        $query = "INSERT INTO redessocialesevento( "
                . " urlredsocialevento, "
                . " idevento,"
                . " idtiporedsocial,"
                . " idusualta,"
                . " fechaalta,"
                . " estatus"
                . " )"
                . " VALUES("
                . "'{$redSocial->getUrlRedSocial()}', "
                . " {$redSocial->getIdEvento()},"
                . " {$redSocial->getIdTipoRedSocial()},"
                . "  {$redSocial->getIdUsuarioAlta()},"
                . "  now(),"
                . "     1"
                . " )";

        if ($this->executeQuery($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function updateRedSocialUrl($redSocial) {
        $query = "UPDATE redessocialesevento
                    SET urlredsocialevento = '{$redSocial->getUrlRedSocial()}',           
                        idusumodifica = {$redSocial->getIdUsuarioModifica()},
                        fechamodifica = now()
                    WHERE  idtiporedsocial = {$redSocial->getIdTipoRedSocial()} AND idevento = {$redSocial->getIdEvento()}";
     
        if ($this->executeQuery($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function exitsRedSocial($idEvento, $idTipoRedSocial) {
        $query = "SELECT * FROM  redessocialesevento
                WHERE  idtiporedsocial = {$idTipoRedSocial} AND idevento = {$idEvento}";
     
        return $this->getAll($query);
    }

    public function addListaInvitados($listaInvitados) {
         $query = "insert into listainvitados (nombreinvitadoprincipal,telefonoinvitadoprincipal,correoinvitadoprincipal,instagraminvitadoprincipal,facebookinvitadoprincipal,idtipoboleto, "
         . "numeroboletosdisponibles,horafingratuito,horafinmedioboleto,idusualta,fechaalta,estatus,urlgenboletos) values( "
            . "'{$listaInvitados->getNombreInvitadoPrincipal()}', "
            . "'{$listaInvitados->getTelefonoInvitadoPrincipal()}', "
            . "'{$listaInvitados->getCorreoInvitadoPrincipal()}', "
            . "'{$listaInvitados->getInstagramInvitadoPrincipal()}', "
            . "'{$listaInvitados->getFacebookInvitadoPrincipal()}', "
            . "{$listaInvitados->getIdTipoBoleto()}, "
            . "{$listaInvitados->grtNumeroBoletosDisponibles()}, "
            . "'{$listaInvitados->getHoraFinGratuito()}', "
            . "{$listaInvitados->getHoraFinMedioBoleto()}, "
            . "{$listaInvitados->getIdUsuAlta()}, "
            . "  now(), " 
            . "'{$listaInvitados->getUrlGenBoletos()}', "
            . " )";
        
        if ($this->executeQuery($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }


    public function addUbicacionEvento($ubicacion) {
        $query = "INSERT INTO ubicacionevento( "
                . " direccionubicacionevento,"
                . " lugar,"
                . " urlubicacionevento, "
                . " referenciasubicacionevento,"
                . " notasubicacionevento,"
                . " iframemapsubicacionevento,"
                . " idevento,"
                . " idusualta,"
                . " fechaalta,"
                . " estatus"
                . " )"
                . " VALUES("
                . "' {$ubicacion->getDireccionUbicacionEvento()}', "
                . "' {$ubicacion->getLugar()}', "
                . "' {$ubicacion->getUrlUbicacionEvento()}',"
                . "' {$ubicacion->getReferenciasUbicacionEvento()}',"
                . "' {$ubicacion->getNotasUbicacionEvento()}',"
                . "' {$ubicacion->getIFrameMapsUbicacionEvento()}',"
                . " {$ubicacion->getIdEvento()},"
                . "  {$ubicacion->getIdUsuarioAlta()},"
                . "  now(),"
                . "     1"
                . " )";

        if ($this->executeQuery($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function deleteDescuentosEvento($idDescuento) {
        $query = "DELETE FROM descuentosevento WHERE iddescuentoevento = {$idDescuento}";

        if ($this->executeDelete($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function deleteEstructurasEvento($idEstructura) {
        $query = "DELETE FROM eventoestructuras WHERE ideventoestructura = {$idEstructura}";

        if ($this->executeDelete($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function deleteMensajesEvento($idMensajeEvento) {
        $query = "DELETE FROM mensajesevento WHERE idmensajeevento = {$idMensajeEvento}";

        if ($this->executeDelete($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function deleteRedesSociales($idRedSocial) {
        $query = "DELETE FROM redessocialesevento WHERE idredsocialevento = {$idRedSocial}";

        if ($this->executeDelete($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function deleteListaInvitados($idListaInvitados) {
        $query = "DELETE FROM listainvitados WHERE idlistainvitados = {$idListaInvitados}";
        
        if ($this->executeDelete($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function deleteUbicacionEvento($idUbicacion) {
        $query = "DELETE FROM ubicacionevento WHERE idubicacionevento = {$idUbicacion}";

        if ($this->executeDelete($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getAllDescuentosEventoByEvento($idEvento) {
        $query = "SELECT de.iddescuentoevento, "
        ."  de.nombredescuentoevento, "
        ."  case "
        ."  when de.condicionesaplicaciondescuento='1' then 'Unico - El Cupon de descuento se aplica de manera individual' "
        ."  when de.condicionesaplicaciondescuento='2' then 'El Cupon de descuento puede aplicarse con más de uno, pero no superar del 20% total' "
        ."  when de.condicionesaplicaciondescuento='3' then 'El Cupon de descuento puede aplicarse con más de uno, pero no suoerar del 10% total' "
        ."  when de.condicionesaplicaciondescuento='4' then 'El Cupon de descuento puede aplicarse con más de uno, pero no suoerar del 5% total' "
        ."  else 'no definido' "
        ."  end as condicionesaplicaciondescuento, "
        ."  de.porcentajemontodescuento, "
        ."  de.clavecupon, "
        ."  de.idevento, "
        ."  de.idusualta, "
        ."  de.fechaalta, "
        ."  de.idusumodifica, "
        ."  de.fechamodifica, "
        ."  de.estatus "
        ." FROM descuentosevento de "
        ." WHERE  de.idevento ={$idEvento}";
        
        return $this->getAll($query);
    }

    public function getAllEstructurasEventoByEvento($idEvento) {
        $query = "SELECT ee.ideventoestructura, "
            ." ee.idtipoelemento, "
            ." cte.nombre as tipoelemento, "
            ." ee.idmultimedia, "
            ." m.urlmultimediaget, "
            ." ee.idevento, "
            ." ee.idusualta, "
            ." ee.fechaalta, "
            ." ee.idusumodifica, "
            ." ee.fechamodifica, "
            ." ee.estatus "
            ." FROM eventoestructuras ee "
            ." INNER JOIN cattipoelemento cte ON cte.id = ee.idtipoelemento "
            ." INNER JOIN multimedia m ON m.idmultimedia = ee.idmultimedia "
            ." WHERE ee.idevento={$idEvento} ORDER BY cte.orden";

        return $this->getAll($query);
    }

    public function getAllMensajesEventoByEvento($idEvento) {
        // $query = "SELECT me.idmensajeevento, "
        // ." me.nombremensaje, "
        // ." me.cuerpomensaje, "
        // ." me.idcatmensaje, "
        // ." cme.nombre as catmensaje, "
        // ." me.idcatenvio, "
        // ." cte.nombre as tipoenvio, "
        // ." me.idevento, "
        // ." me.idusualta, "
        // ." me.fechaalta, "
        // ." me.idusumodifica, "
        // ." me.fechamodifica, "
        // ." me.estatus "
        // ." FROM mensajesevento me "
        // ." INNER JOIN catmensajeevento cme ON cme.id = me.idcatmensaje "
        // ." INNER JOIN cattipoenvio cte ON cte.id = me.idcatenvio "
        // ." WHERE me.idevento={$idEvento}";
        $query = "select idmensajeevento,sms,correo,notas,idevento from mensajeseventoconfirmacion where idevento = {$idEvento}";
        
        $data= $this->getAll($query);
        if ($data){
            return $data[0];
        }else{
            return false;
        }
    }

    public function getAllRedesSocialesByEvento($idEvento) {
        $query = "SELECT rs.idredsocialevento, "
        ." rs.urlredsocialevento, "
        ." rs.idevento, "
        ." rs.idtiporedsocial, "
        ." ctrs.nombre tiporedsocial, "
        ." rs.idusualta, "
        ." rs.fechaalta, "
        ." rs.idusumodifica, "
        ." rs.fechamodifica, "
        ." rs.estatus "
        ." FROM redessocialesevento rs "
        ." INNER JOIN cattiporedsocial ctrs ON ctrs.id = rs.idtiporedsocial " 
         ." WHERE rs.idevento={$idEvento}";

        return $this->getAll($query);
    }
    public function getAllListaInvidatosByEvento($idEvento) { /*revisar*/
        $query = "select li.apminvitado, "
        ."li.appinvitado, "
        ."li.claveproductor, "
        ."li.estadoboletos, "
        ."li.idevento, "
        ."li.idlistainvitados, "
        ."li.nombreinvitado, "
        ."li.numeroboletosdisponibles, "
        ."li.numeroboletosgenerados, "
        ."li.correoinvitado, "
        ."li.telefonoinvitado, "
        ."li.urlgeneracionboletos from listainvitados li inner join eventos e  on li.idevento= e.idevento "
         ." WHERE e.idevento={$idEvento}";
        return $this->getAll($query);
    }

    public function getDescuentosEvento($idDescuento) {
        $query = "SELECT * FROM descuentosevento WHERE iddescuentoevento={$idDescuento}";

        return $this->getAll($query);
    }

    public function getEstructurasEvento($idEstructura) {
        $query = "SELECT * FROM eventoestructuras WHERE ideventoestructura={$idEstructura}";

        return $this->getAll($query);
    }

    public function getMensajeEvento($idMensajeEvento) {
        $query = "SELECT * FROM mensajesEvento  WHERE idmensajeevento={$idMensajeEvento}";

        return $this->getAll($query);
    }

    public function getRedesSociales($idRedSocial) {
        $query = "SELECT * FROM redessocialesevento WHERE idredsocialevento={$idRedSocial}";

        return $this->getAll($query);
    }
    public function getListaInvitados($idListaInvidatos){
        $query = "SELECT * FROM listainvitados WHERE idlistainvitados={$idListaInvidatos}";

        return $this->getAll($query);
    }

    public function getUbicacionEvento($idUbicacion) {
        $query = "SELECT * FROM ubicacionevento WHERE idubicacionevento={$idUbicacion}";

        return $this->getAll($query);
    }

    public function getAllUbicacionesEventoByEvento($idEvento) {
        $query = "SELECT  ue.idubicacionevento, "
        ." ue.direccionubicacionevento, "
        ." ue.lugar, "
        ." ue.urlubicacionevento, "
        ." ue.referenciasubicacionevento, "
        ." ue.notasubicacionevento, "
        ." ue.iframemapsubicacionevento, "
        ." ue.idevento, m.urlmultimediaget from ubicacionevento ue inner join eventos e ON ue.idevento = e.idevento "
        ." left join eventoestructuras ee on e.idevento = ee.idevento and ee.idtipoelemento=7 "
        ." left join multimedia m on ee.idmultimedia = m.idmultimedia "
        ." WHERE ue.idevento ={$idEvento}";

        return $this->getAll($query);

    }

    public function updateDescuentosEvento($descuento) {
        $query = "UPDATE descuentosevento SET"
                . " nombredescuentoevento ='{$descuento->getNombreDescuento()}',"
                . " condicionesaplicaciondescuento ='{$descuento->getCondicionesAplicacionDescuento()}', "
                . " porcentajemontodescuento ='{$descuento->getPorcentajeMontoDescuento()}',"
                . " clavecupon ={$descuento->getClaveCupon()}, "
                . " idevento ={$descuento->getIdEvento()},"
                . " idusumodifica={$descuento->getIdUsuarioModifica()}, "
                . " fechamodifica=now() "
                . " WHERE idevento = {$descuento->getIdEvento()}";
        if ($this->executeQuery($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function updateEstructurasEvento($estructura) {
        $query = "UPDATE eventoestructuras SET"
                . " idtipoelemento ={$estructura->getIdTipoElemento()},"
                . " idmultimedia ={$estructura->getIdMultimedia()}, "
                . " idevento ={$estructura->getIdEvento()},"
                . " idusumodifica={$estructura->getIdUsuarioModifica()}, "
                . " fechamodifica=now() "
                . " WHERE idevento = {$estructura->getIdEvento()}";

        if ($this->executeQuery($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function updateRedesSociales($redSocial) {
        $query = "UPDATE redessocialesevento SET"
                . " urlredsocialevento ='{$redSocial->getUrlRedSocial()}',"
                . " idtiporedsocial={$redSocial->getIdTipoRedSocial()}, "
                . " idevento ={$redSocial->getIdEvento()},"
                . " idusumodifica={$redSocial->getIdUsuarioModifica()}, "
                . " fechamodifica=now() "
                . " WHERE idredsocialevento = {$redSocial->getIdRedSocialEvento()}";

        if ($this->executeQuery($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function  updateListaInvitatos($listaInvitados) {
        $query = "UPDATE listainvitados SET "
        . " nombreinvitado='{$listaInvitados->getNombreInvitado()}',"
        . " appinvitado='{$listaInvitados->getAppInvitado()}',"
        . " apminvitado='{$listaInvitados->getApmInvitado()}',"
        . " numeroboletosdisponibles={$listaInvitados->getNumeroBoletosDisponibles()}',"
        . " numeroboletosgenerados={$listaInvitados->getNumeroBoletosGenerados()}," 
        . " claveproductor='{$listaInvitados->getClaveProductor()}'," 
        . " urlgeneracionboletos='{$listaInvitados->getUrlGeneracionBoletos()}'," 
        . " urlgeneracionboletos='{$listaInvitados->getCorreoInvitados()}'," 
        . " urlgeneracionboletos='{$listaInvitados->getTelefonoInvitados()}'," 
        . " estadoboletos=1," 
        . " idusumodifica={$listaInvitados->getIdUsuarioModifica()}'," 
        . " fechamodifica=now() ,"
        . " WHERE idlistainvitados={$listaInvitados->getIdListaInvitados()}," ;
        
        if ($this->executeQuery($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function updateUbicacionEvento($ubicacion) {
        $query = "UPDATE ubicacionevento SET"
                . " direccionubicacionevento ='{$ubicacion->getDireccionUbicacionEvento()}',"
                . " lugar ='{$ubicacion->getLugar()}',"
                . " urlubicacionevento ='{$ubicacion->getUrlUbicacionEvento()}', "
                . " referenciasubicacionevento ='{$ubicacion->getReferenciasUbicacionEvento()}',"
                . " notasubicacionevento ='{$ubicacion->getNotasUbicacionEvento()}', "
                . " iframemapsubicacionevento ='{$ubicacion->getIFrameMapsUbicacionEvento()}', "
                . " idevento ={$ubicacion->getIdEvento()},"
                . " idusumodifica={$ubicacion->getIdUsuarioModifica()}, "
                . " fechamodifica=now() "
                . " WHERE idevento = {$ubicacion->getIdEvento()}";

        if ($this->executeQuery($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function getEventEstructura($evento){
        $query = "SELECT ee.idevento, "
                ."        cte.nombre, "
                ."        m.urlmultimediaget "
                ." FROM eventoestructuras ee "
                ." INNER JOIN cattipoelemento cte ON cte.id = ee.idtipoelemento "
                ." INNER JOIN multimedia m ON m.idmultimedia = ee.idmultimedia "
                ." WHERE ee.idevento = {$evento} ORDER BY cte.orden";

        return $this->getAll($query);
    }
    public function getEventPatrociandores($evento){
        $query = " SELECT ep.idevento,"
                ."        m.urlmultimediaget,"
                ."        p.urlpatrocinador,"
                ."        p.nombrepatrocinador"
                ." FROM multimedia m"
                ."   INNER JOIN patrocinadores p ON p.idimagenpatrocinador = m.idmultimedia"
                ."   INNER JOIN eventopatrocinadores ep"
                ."           ON ep.idpatrocinador = p.idpatrocinador"
                ."          AND ep.idevento = {$evento} ";

        return $this->getAll($query);
    }

    public function addTipoBoletoEvento($tipoBoleto){
        $query = "INSERT INTO tipoboleto
            (          
            tipoboleto,
            tipoboletodesc,
            precioboleto,
            cantidadboletos,
            idevento,
            idusualta,
            fechaalta,          
            estatus
            )
            VALUES
            (          
            '{$tipoBoleto->getTipoBoleto()}',
            '{$tipoBoleto->getTipoBoletoDesc()}',
            '{$tipoBoleto->getPrecio()}',
            {$tipoBoleto->getCantidadBoletos()},
            {$tipoBoleto->getidEvento()},
            {$tipoBoleto->getIdUsuarioAlta()},
            now(),          
            1
            );";

        if ($this->executeQuery($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function updateTipoBoletoEvento($tipoBoleto){
        $query = "UPDATE tipoboleto
            SET tipoboleto = '{$tipoBoleto->getTipoBoleto()}',
                tipoboletodesc = '{$tipoBoleto->getTipoBoletoDesc()}',
                precioboleto = '{$tipoBoleto->getPrecio()}',
                cantidadboletos = {$tipoBoleto->getCantidadBoletos()},                                    
                idusumodifica = {$tipoBoleto->getIdUsuarioModifica()},
                fechamodifica = now()           
        WHERE idtipoboleto = {$tipoBoleto->getIdTipoBoleto()};";

        if ($this->executeQuery($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function getAllTipoBoletoByEvento($idEvento) {
        $query = "SELECT tb.*, 
        (SELECT SUM(numboletosimpresos) FROM boletosfisicos WHERE idtipoboleto = tb.idtipoboleto) as boletosfisicos,
        (SELECT SUM(numeroboletosdisponibles) FROM cortesias WHERE idtipoboleto = tb.idtipoboleto) as cortesias
         FROM tipoboleto tb
         WHERE tb.idevento = {$idEvento} and tb.estatus =1
         ORDER BY tb.precioboleto ASC

";
        return $this->getAll($query);

    }
    public function getAllActiveTipoBoletoByEvento($idEvento) {
        $query = "SELECT tb.*, 
        (SELECT SUM(numboletosimpresos) FROM boletosfisicos WHERE idtipoboleto = tb.idtipoboleto) as boletosfisicos,
        (SELECT SUM(numeroboletosdisponibles) FROM cortesias WHERE idtipoboleto = tb.idtipoboleto) as cortesias
         FROM tipoboleto tb
         WHERE tb.idevento = {$idEvento} AND tb.estatus = 1
         ORDER BY tb.precioboleto ASC

";
        return $this->getAll($query);

    }
    public function getTipoBoletoById($idTipoBoleto){
        $query = "SELECT  * FROM tipoboleto WHERE idtipoboleto ={$idTipoBoleto}";
        return $this->getAll($query);
    }
    public function deleteTipoBoletoById($id) {
        $query = "DELETE FROM tipoboleto WHERE idtipoboleto = {$id}";

        if ($this->executeDelete($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function addUsuarioAppEvento($usuarioApp){
        $query = "INSERT INTO usuariosapp
            (            
            nip,
            idusuario,
            idevento,
            idusualta,
            fechaalta,                        
            estatus
            )
            VALUES
            (            
            '{$usuarioApp->getNip()}',
            {$usuarioApp->getIdUsuario()},
            {$usuarioApp->getIdEvento()},
            {$usuarioApp->getIdUsuarioAlta()},
            now(),                        
            1
            );";

        if ($this->executeQuery($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function getAllUsuariosAppByEvento($idEvento) {
        $query = "SELECT ua.idusuarioapp,
                            ua.nip,
                            u.nombreusu,
                            u.appusu,
                            u.ampusu,
                            u.correousu,
                            ctu.nombre AS tipousuario
                    FROM usuariosapp ua
                    INNER JOIN usuarios u ON u.idusuario = ua.idusuario
                    INNER JOIN cattipousuario ctu ON ctu.id = u.idtipousuario
                    WHERE ua.idevento ={$idEvento}";
        return $this->getAll($query);
    }
    public function existsUsuarioAppEvento($idEvento, $idUsuario) {
        $query = "SELECT  * FROM usuariosapp WHERE idevento ={$idEvento} AND idusuario={$idUsuario}";
        
        if(count($this->getAll($query)) > 0){
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function deleteUsuarioAppById($id) { 
        $query = "DELETE FROM usuariosapp WHERE idusuarioapp = {$id}";

        if ($this->executeDelete($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    Public function getAllUsuariosRelPublicByEvento($idEvento) {
        $query = "SELECT rp.idrelacionespublicas,u.nombreusu,u.appusu,u.ampusu,u.correousu,ctu.nombre AS tipousuario, CONCAT(ua.nombreusu,' ',ua.appusu,' ',ua.ampusu) creado_por
        FROM relacionespublicas rp 
        INNER JOIN  usuarios u ON rp.idusuario = u.idusuario
         INNER JOIN  usuarios ua ON ua.idusuario = u.idusuarioalta
        INNER JOIN cattipousuario ctu ON ctu.id = u.idtipousuario
        WHERE rp.idevento={$idEvento}";
        
        return $this->getAll($query);
    }
    public function existsUsuarioRPEvento($idEvento, $idUsuario) {
        $query = "SELECT  * FROM relacionespublicas WHERE idevento ={$idEvento} AND idusuario={$idUsuario}";
        
        if(count($this->getAll($query)) > 0){
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function addUsuarioRPEvento($usuarioApp){
        $query = "INSERT INTO relacionespublicas "
        ."(idevento, idusuario) "
        ."VALUES( " 
        ."{$usuarioApp->getIdEvento()}, "
        ."{$usuarioApp->getIdUsuario()} "
        .");";
        

        if ($this->executeQuery($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function deleteUsuarioRPById($id) { 
        $query = "DELETE FROM relacionespublicas WHERE idrelacionespublicas = {$id}";
        
        if ($this->executeDelete($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getBoletosCreadosEvento($idEvento)
    {
        $query = "SELECT SUM(cantidadboletos) boletosEvento  FROM tipoboleto WHERE idevento ={$idEvento}";
        $result= $this->getAll($query);
        if($result && !empty($result[0]['boletosEvento'])){
            return $result[0]['boletosEvento'];
        }else{
            return 0;
        }
    }
    public function getCupoEvento($idEvento){
        $query = "SELECT ocupacion FROM eventos WHERE idevento ={$idEvento}";
        $result= $this->getAll($query);
        if($result && !empty($result[0]['ocupacion'])){
            return $result[0]['ocupacion'];
        }else{
            return 0;
        }
    }

    //para reportes
    public function getBoletosVendidos(){
         echo $query = "select k.idkardexboletos,k.idtipoboleto,k.folioboletos,k.nombrepersona,k.correopersona,k.telefonopersona,k.asiento,k.estatus, "
        ."t.tipoboleto, t.precioboleto, "
        ."e.nombreevento, e.idusualta , "
        ."u.nombreusu, u.appusu, u.ampusu, o.idordercompra, o.rp, "
        ."case when exists (select idkardexboletos from listakardex lk where lk.idkardexboletos =k.idkardexboletos) then 'Lista Invitado' "
        ."    when exists(select idkardexboletos from cortesiakardex ck where ck.idkardexboletos =k.idkardexboletos) then 'Cortesia' "
        ."  when EXISTS (select idkardexboletos from fisicokardex gk where gk.idkardexboletos =k.idkardexboletos ) then 'Boleto fisico' "
        ."  when EXISTS (select idkardexboletos from detalleordencompra d where d.idkardexboletos = k.idkardexboletos) then "
        ."        case when  EXISTS (select * from ordencompra o2 where o2.idordercompra= o.idordercompra and o2.rp<>'') then'Compra relaciones publicas ' "
        ."          else 'Compra online' "
        ."          end "
        ."else 'NO' "
        ."end as tipo_boleto "
        ."from kardexboletos k INNER JOIN tipoboleto t on k.idtipoboleto = t.idtipoboleto "
        ."INNER JOIN eventos e on t.idevento = e.idevento "
        ."INNER JOIN usuarios u on e.idusualta = u.idusuario" 
        ."LEFT JOIN detalleordencompra do on  do.idkardexboletos = k.idkardexboletos " 
        ."LEFT JOIN ordencompra o on o.idordercompra =do.idordercompra"; 
        exit();
        return $this->getAll($query);
    }

    public function getOrdenCompraByEventoIdAndRelacionesPublicasId($idEvento, $idRelacionesPublicas){
        $query = " SELECT * FROM ordencompra o WHERE o.idevento= {$idEvento} AND o.rp={$idRelacionesPublicas}";

        return $this->getAll($query);
    }

    public function getOrdenCompraByEventoIdAndUsuarioProductorId($idEvento, $idProductor){
        $query = " SELECT * FROM ordencompra o 
        JOIN eventos e ON e.idevento = o.idevento 
        WHERE o.idevento= {$idEvento} AND e.idusuario={$idProductor}";

        return $this->getAll($query);
    }
    public function updateStatus($idEvent, $newStatus){
        $query = "UPDATE eventos SET estatus ={$newStatus} WHERE idevento = {$idEvent}";        
        
        if ($this->executeQuery($query)) {            
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function publicarEvento($idEvent){
        $query = "UPDATE eventos SET idtipoevento=45  WHERE idevento = {$idEvent}";        
        
        if ($this->executeQuery($query)) {            
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getReporteVentasPorEvento($idEvento)
    {
            $query = "select o.idordercompra as idordercompra, o.fechaalta as fechacompra_envio,o.estatus as estatusPago_id,o.totalcompra,
            (((select SUM(CASE WHEN k.estatus=1 THEN IF( t.precioboleto<0,0,t.precioboleto) ELSE 0 end) from kardexboletos k inner join tipoboleto t on t.idtipoboleto =k.idtipoboleto inner join detalleordencompra d on d.idkardexboletos =k.idkardexboletos where d.idordercompra=o.idordercompra)* 
            (select u.porcentajecomision from eventos e inner join usuarios u ON  e.idusuario = u.idusuario where e.idevento =o.idevento))/100)comision,
            o.numboletoscompra,
            case when (
            	select k.nombrepersona from kardexboletos k inner join detalleordencompra d2 on k.idkardexboletos =d2.idkardexboletos where d2.idordercompra = o.idordercompra limit 1)<>'' 
            then (select  k.nombrepersona from kardexboletos k inner join detalleordencompra d2 on k.idkardexboletos =d2.idkardexboletos where d2.idordercompra = o.idordercompra limit 1) 
            else ''
            end cliente,
            case when 
            	(select  k.correopersona from kardexboletos k inner join detalleordencompra d2 on k.idkardexboletos =d2.idkardexboletos Where d2.idordercompra = o.idordercompra limit 1)<>'' 
            then (select  k.correopersona from kardexboletos k inner join detalleordencompra d2 on k.idkardexboletos =d2.idkardexboletos where d2.idordercompra = o.idordercompra limit 1) 
            else '' 
            end  as correo_cliente,
           case when (select  k.telefonopersona from kardexboletos k inner join detalleordencompra d2 on k.idkardexboletos =d2.idkardexboletos where d2.idordercompra = o.idordercompra limit 1) <>''
            then (select  k.telefonopersona from kardexboletos k inner join detalleordencompra d2 on k.idkardexboletos =d2.idkardexboletos where d2.idordercompra = o.idordercompra limit 1)
           else '' 
            end  as celular_clientes ,
            CASE o.estatus
                WHEN 1 and o.metodopago = 'card' THEN 'Pagado / pago con tarjeta online'
                WHEN 1 and o.metodopago = 'store' THEN 'Pagado / pago en tienda'
                WHEN 1 and o.metodopago = 'cash' THEN 'Pagado / venta fisica portal RP'
               	WHEN 2   THEN 'Pendiente / no se ha procesado el pago'
                WHEN 10 THEN 'Pendiente/ pago en abonos'
                when 11 then 'Pagado/pago en abonos'
                When -1 THEN 'Vencido'
                when -3 then 'Orden cancelada'
            END as 'estatusPago',
            case when o.rp <>'' then 1 else 0 end as  venta_rp,
           case when  (CONCAT(urp.nombreusu, ' ', urp.appusu, ' ', urp.ampusu))<>'' then (CONCAT(urp.nombreusu, ' ', urp.appusu, ' ', urp.ampusu)) else '' end  nombre_relacionesP, 
            case when (urp.correousu) <>'' then (urp.correousu) else '' end as correo_relacionesP 
            from ordencompra o left join usuarios urp on o.rp= urp.idusuario 
            where o.idevento ={$idEvento} and o.estatus in (1,2,10,11,-3)" ; 

        
        
        return $this->getAll($query);
    }
    public function findAddUrlEvento($url){
        $query = "SELECT * FROM eventos WHERE urlevento = '{$url}'";       
        
        return $this->getAll($query);
    }


    public function getReporteAbonoPorEvento($idEvento)
    {
            $query = "select o.idevento, e.nombreevento,idordercompra , numboletoscompra , totalcompra, o.idusuario, concat(u.nombreusu, ' ', u.appusu, ' ', u.ampusu) as nombre_cliente,u.correousu,  
            CASE o.estatus
               WHEN 10 THEN 'Pendiente/ pago en abonos'
               when 11 then 'Pagado/pago en abonos'
           END as 'estatusPago',
           (select group_concat(concat(' - ',fechapago,' - $',montopagado ) SEPARATOR '<br>----------<br>' ) from abonospago a where a.idordercompra=o.idordercompra order by fechapago ) as abonos
           from ordencompra o inner join usuarios u on u.idusuario =o.idusuario 
           inner join eventos e on e.idevento =o.idevento 
           where o.estatus in (10,11) and o.idevento={$idEvento}"; 
        
        return $this->getAll($query);
    }

// Reporte de puntos de venta 
    public function getReportePVPorEvento($idEvento)
    {
            $query = " select sum(if(k.idusuario_activar<>'',1,0)) activados,  k.idusuario_activar, t.idevento, CONCAT(u.nombreusu, ' ', u.appusu, ' ', u.ampusu)as nombre, 
            t.idtipoboleto, t.idevento 
            from kardexboletos k
            inner join tipoboleto t on k.idtipoboleto = t.idtipoboleto 
            left join usuarios u on k.idusuario_activar=u.idusuario 
           where t.idevento ={$idEvento} group by idusuario_activar"; 
        
        return $this->getAll($query);
    }
    // Reporte de Accesos a eventos
    public function getReporteAEPorEvento($idEvento)
    {
            $query = " select sum(if(k.ingreso=1,1,0)) ingresos,  k.idusuario_ingreso, t.idevento, CONCAT(u.nombreusu, ' ', u.appusu, ' ', u.ampusu)as nombre
            from kardexboletos k
            inner join tipoboleto t on k.idtipoboleto = t.idtipoboleto 
            left join usuarios u on k.idusuario_ingreso=u.idusuario 
           where t.idevento ={$idEvento} group by idusuario_ingreso, t.idevento"; 
        
        return $this->getAll($query);
    }

    
    public function getReporteventasrpPorEvento($idEvento)
    {
            $query = "select o.idordercompra, o.numboletoscompra, o.idevento, o.fechaalta, o.rp, concat(urp.nombreusu,' ', urp.appusu, ' ', urp.ampusu) as nombre_rp, 
            urp.correousu, o.estatus,
            CASE o.estatus
               WHEN 1 and metodopago = 'cash' THEN 'Pagado/ venta fisica RP'
               WHEN 1 and metodopago = 'card' THEN 'Pagado/ venta por portal RP, online'
                WHEN 1 and metodopago = 'store' THEN 'Pagado/ venta por portal RP, pago tienda'
               END as 'estatusPago'
            from ordencompra o  
            inner join usuarios urp on o.rp =urp.idusuario 
            where o.rp<>''  and o.estatus =1 and o.idevento={$idEvento}"; 
        
        return $this->getAll($query);
    }

    public function findUpdateUrlEvento($url, $idEvento){
        $query = "SELECT * FROM eventos WHERE urlevento = '{$url}' AND idevento <> {$idEvento}";
        
        return $this->getAll($query);
    }
    public function getIdEventoByUrlEvento($urlEvento){
        $query = "
        SELECT DISTINCT e.idevento, rp.idusuario FROM eventos e
        LEFT JOIN relacionespublicas rp ON rp.idevento = e.idevento
        WHERE CONCAT(e.urlevento,rp.rpcomplementourl) LIKE '{$urlEvento}' OR e.urlevento LIKE '{$urlEvento}'";
        $response = $this->getAll($query);
        if(count($response) > 0) {
            return $response[0];
        } else {
            return FALSE;
        }
    }
    public function getIdRPByUrlEvento($urlEvento){
        $query = "
        SELECT DISTINCT e.idevento, rp.idusuario FROM eventos e
        LEFT JOIN relacionespublicas rp ON rp.idevento = e.idevento
        WHERE CONCAT(e.urlevento,rp.rpcomplementourl) LIKE '{$urlEvento}'";
        $response = $this->getAll($query);
        if(count($response) > 0) {
            return $response[0];
        } else {
            return FALSE;
        }
    }

    public function getReporteTableroPorEvento($idEvento)
    {
            $query = "select  ( select sum(if(k2.ingreso in (1),1,0)) as escaneados  from kardexboletos k2  left join tipoboleto t2 on k2.idtipoboleto = t2.idtipoboleto  left  JOIN
            eventos e2 on  t2.idevento = e2.idevento where e2.idevento=e.idevento)as escaneados,e.idevento, (select sum(t2.cantidadboletos) from tipoboleto t2 where t2.idevento =e.idevento)as boletos_creados,
            ((select sum(t2.cantidadboletos) from tipoboleto t2 where t2.idevento =e.idevento)- sum(if(k.estatus in (1,2,6,5,3,10,11),1,0))) as restantes, 
            sum(if(k.estatus in (10,2),1,0)) as pago_transicion, 
            sum(if(k.estatus in (1,11),1,0)) as boletos_vendidos,  
            (select TRUNCATE((( select SUM(CASE WHEN k3.estatus=1 THEN IF( t3.precioboleto<0,0,t3.precioboleto) ELSE 0 end) from kardexboletos k3 
            inner join tipoboleto t3 on t3.idtipoboleto =k3.idtipoboleto  inner join detalleordencompra d3 on d3.idkardexboletos =k3.idkardexboletos 
            inner join ordencompra o3 on o3.idordercompra =d3.idordercompra  and o3.metodopago not in ('cash') where t3.idevento =e.idevento)* 
            (select u3.porcentajecomision from eventos e3 inner join usuarios u3 ON  e3.idusuario = u3.idusuario where e3.idevento =e.idevento ))/100,2))   as comision_venta,
            (select sum(case when d2.porcentajemontodescuento IS not NULL then ( t.precioboleto - ((t.precioboleto)/100) * d2.porcentajemontodescuento)
            else t.precioboleto end )
            from kardexboletos k inner join detalleordencompra d on k.idkardexboletos =d.idkardexboletos 
            inner join ordencompra o on d.idordercompra =o.idordercompra  and o.metodopago not in ('cash')
            inner join tipoboleto t on t.idtipoboleto =k.idtipoboleto 
            left join ordencompradescuentos o2 on o.idordercompra =o2.idordercompra 
            left join descuentosevento d2 on o2.iddescuentoevento = d2.iddescuentoevento 
            where k.estatus =1 and o.idevento=e.idevento) as total_ingresos,
            count(CASE WHEN EXISTS ( select k2.idkardexboletos from kardexboletos k2 
                   inner join detalleordencompra d on k2.idkardexboletos= d.idkardexboletos 
                   inner join ordencompra o on d.idordercompra =o.idordercompra 
                      and o.metodopago = 'card' and k2.estatus  in (1)
              where  k2.idkardexboletos   = k.idkardexboletos 
              and k2.idtipoboleto = t.idtipoboleto and o.rp IS NULL
              ) THEN 1 END ) as boletos_online,
            count(CASE WHEN EXISTS ( select k2.idkardexboletos from kardexboletos k2 
                   inner join detalleordencompra d on k2.idkardexboletos= d.idkardexboletos 
                   inner join ordencompra o on d.idordercompra =o.idordercompra 
                      and o.metodopago = 'store' and k2.estatus in (1)
                      where  k2.idkardexboletos   = k.idkardexboletos 
                      and k2.idtipoboleto = t.idtipoboleto 
                      ) THEN 1 END ) as compra_tienda,
            COUNT(CASE WHEN EXISTS (SELECT idkardexboletos FROM listakardex lk WHERE lk.idkardexboletos = k.idkardexboletos and lk.idkardexboletos not in (select d2.idkardexboletos  from detalleordencompra d2)) THEN 1 END ) AS lista_invitados,
            COUNT(CASE WHEN EXISTS (SELECT idkardexboletos FROM cortesiakardex ck WHERE ck.idkardexboletos = k.idkardexboletos and ck.idkardexboletos not in (select d3.idkardexboletos  from detalleordencompra d3)) THEN 1 END ) as cortesias,
            COUNT(CASE WHEN EXISTS (SELECT idkardexboletos FROM fisicokardex gk WHERE gk.idkardexboletos = k.idkardexboletos and gk.idkardexboletos not in (select d4.idkardexboletos  from detalleordencompra d4)) THEN 1 END) AS fisicos,
            (select count(numboletoscompra) from ordencompra o  inner join detalleordencompra d ON o.idordercompra = d.idordercompra and o.estatus=1 
            where o.rp<>'' and o.idevento=e.idevento) as venta_rp
             from eventos e
            left  JOIN tipoboleto t ON t.idevento = e.idevento
            left JOIN kardexboletos k  ON k.idtipoboleto = t.idtipoboleto where e.idevento={$idEvento}"; 
        $response= $this->getAll($query);
        if (count($response)>0){
            return$response[0];
        } else {
            return FALSE;
        }
    }
    //Detalle del boleto
    public function getDetalleBoleto($idordencompra)
    {
            $query = "select o.idordercompra as idordercompra, o.fechaalta as fechacompra_envio,o.estatus as estatusPago_id,o.totalcompra,
            TRUNCATE(((select SUM(CASE WHEN k.estatus=1 THEN IF( t.precioboleto<0,0,t.precioboleto) ELSE 0 end) from kardexboletos k inner join tipoboleto t on t.idtipoboleto =k.idtipoboleto inner join detalleordencompra d on d.idkardexboletos =k.idkardexboletos where d.idordercompra=o.idordercompra)* 
            (select u.porcentajecomision from eventos e inner join usuarios u ON  e.idusuario = u.idusuario where e.idevento =o.idevento))/100,2)comision,
            o.numboletoscompra,
            case when (select  k.nombrepersona from kardexboletos k inner join detalleordencompra d2 on k.idkardexboletos =d2.idkardexboletos where d2.idordercompra = o.idordercompra limit 1) <> '' then (select  k.nombrepersona from kardexboletos k inner join detalleordencompra d2 on k.idkardexboletos =d2.idkardexboletos where d2.idordercompra = o.idordercompra limit 1) else'' end as cliente,
            (select  k.correopersona from kardexboletos k inner join detalleordencompra d2 on k.idkardexboletos =d2.idkardexboletos where d2.idordercompra = o.idordercompra limit 1) as correo_cliente,
            (select  k.telefonopersona from kardexboletos k inner join detalleordencompra d2 on k.idkardexboletos =d2.idkardexboletos where d2.idordercompra = o.idordercompra limit 1) as celular_clientes ,
            CASE o.estatus
                WHEN 1 and o.metodopago = 'card' THEN 'Pagado / pago con tarjeta online'
                WHEN 1 and o.metodopago = 'store' THEN 'Pagado / pago en tienda'
                WHEN 1 and o.metodopago = 'cash' THEN 'Pagado / venta fisica portal RP'
               	WHEN 2   THEN 'Pendiente / no se ha procesado el pago'
                WHEN 10 THEN 'Pendiente/ pago en abonos'
                when 11 then 'Pagado/pago en abonos'
                When -1 THEN 'Vencido'
                when -3 then 'Orden cancelada'
            END as 'estatusPago',
            case when o.rp <>'' then 1 else 0 end as  venta_rp,
           case when  (CONCAT(urp.nombreusu, ' ', urp.appusu, ' ', urp.ampusu))<>'' then (CONCAT(urp.nombreusu, ' ', urp.appusu, ' ', urp.ampusu)) else '' end  nombre_relacionesP, 
            case when (urp.correousu) <>'' then (urp.correousu) else '' end  correo_relacionesP , 
            (select  GROUP_CONCAT(concat('Folio: ',k2.folioboletos,'<br>','Tipo boleto: ',  t.tipoboleto, '<br>', 'Estado boleto: ', CASE k2.estatus when 1 then  'Pagado' when 2 then 'Pendiente de pago' when -1 then 'Pago vencido' end , 
                  '<br>','Ingreso: ', case k2.ingreso when 1 then 'Ingresado' when 0 then 'No ingresado'end ) SEPARATOR '<br><br>') AS boletos
                  from kardexboletos k2 inner join tipoboleto t on k2.idtipoboleto =t.idtipoboleto 
                  inner join detalleordencompra d2 on k2.idkardexboletos = d2.idkardexboletos 
                 where d2.idordercompra = o.idordercompra) AS boletos
            from ordencompra o left join usuarios urp on o.rp= urp.idusuario 
            where o.idordercompra={$idordencompra}"; 
        $response= $this->getAll($query);
        if (count($response)>0){
            return$response[0];
        } else {
            return FALSE;
        }
    }

    //Detalle del boleto
    public function getDetalleOrdenRP($idordencompra)
    {
            $query = "select o.idordercompra as idordercompra, o.fechaalta as fechacompra_envio,o.estatus as estatusPago_id,o.totalcompra,
            TRUNCATE(((select SUM(CASE WHEN k.estatus=1 THEN IF( t.precioboleto<0,0,t.precioboleto) ELSE 0 end) from kardexboletos k inner join tipoboleto t on t.idtipoboleto =k.idtipoboleto inner join detalleordencompra d on d.idkardexboletos =k.idkardexboletos where d.idordercompra=o.idordercompra)* 
            (select u.porcentajecomision from eventos e inner join usuarios u ON  e.idusuario = u.idusuario where e.idevento =o.idevento))/100,2)comision,
            o.numboletoscompra,
            case when (select  k.nombrepersona from kardexboletos k inner join detalleordencompra d2 on k.idkardexboletos =d2.idkardexboletos where d2.idordercompra = o.idordercompra limit 1) <> '' then (select  k.nombrepersona from kardexboletos k inner join detalleordencompra d2 on k.idkardexboletos =d2.idkardexboletos where d2.idordercompra = o.idordercompra limit 1) else'' end as cliente,
            (select  k.correopersona from kardexboletos k inner join detalleordencompra d2 on k.idkardexboletos =d2.idkardexboletos where d2.idordercompra = o.idordercompra limit 1) as correo_cliente,
            (select  k.telefonopersona from kardexboletos k inner join detalleordencompra d2 on k.idkardexboletos =d2.idkardexboletos where d2.idordercompra = o.idordercompra limit 1) as celular_clientes ,
            CASE o.estatus
                WHEN 1 and metodopago = 'cash' THEN 'Pagado/ venta fisica RP'
                WHEN 1 and metodopago = 'card' THEN 'Pagado/ venta por portal RP, online'
                WHEN 1 and metodopago = 'store' THEN 'Pagado/ venta por portal RP, pago tienda'
               	WHEN 2   THEN 'Pendiente / no se ha procesado el pago'
                WHEN 10 THEN 'Pendiente/ pago en abonos'
                when 11 then 'Pagado/pago en abonos'
                When -1 THEN 'Vencido'
                when -3 then 'Orden cancelada'
            END as 'estatusPago',
            case when o.rp <>'' then 1 else 0 end as  venta_rp,
           case when  (CONCAT(urp.nombreusu, ' ', urp.appusu, ' ', urp.ampusu))<>'' then (CONCAT(urp.nombreusu, ' ', urp.appusu, ' ', urp.ampusu)) else '' end  nombre_relacionesP, 
            case when (urp.correousu) <>'' then (urp.correousu) else '' end  correo_relacionesP , 
            (select  GROUP_CONCAT(concat('Folio: ',k2.folioboletos,'<br>','Tipo boleto: ',  t.tipoboleto, '<br>', 'Estado boleto: ', CASE k2.estatus when 1 then  'Pagado' when 2 then 'Pendiente de pago' when -1 then 'Pago vencido' end , 
                  '<br>','Ingreso: ', case k2.ingreso when 1 then 'Ingresado' when 0 then 'No ingresado'end ) SEPARATOR '<br><br>') AS boletos
                  from kardexboletos k2 inner join tipoboleto t on k2.idtipoboleto =t.idtipoboleto 
                  inner join detalleordencompra d2 on k2.idkardexboletos = d2.idkardexboletos 
                 where d2.idordercompra = o.idordercompra) AS boletos
            from ordencompra o left join usuarios urp on o.rp= urp.idusuario 
            where o.idordercompra={$idordencompra}"; 
        $response= $this->getAll($query);
        if (count($response)>0){
            return$response[0];
        } else {
            return FALSE;
        }
    }

//Detalle de ventas -reporte integral
//public function getDetalleVentas($eventos,$fechainicio,$fechafin)
public function getDetalleVentas($eventos,$fechainicio,$fechafin)
{
    $where='';
    // var_dump($eventos);
    // exit();
    if($eventos!=''){
        
        $in = implode(",", $eventos);
        $where .= " and e.idevento in ({$in})";
    }

    if ($fechainicio !='' && $fechafin!= ''){
        $where .=" and o.fechaalta BETWEEN '{$fechainicio} 00:00:00' and '{$fechafin} 23:59:59'";
    }
    $query = "select o.fechaalta, o.idordercompra, e.nombreevento,
                truncate(((sum(case when k.estatus =1 THEN IF( t.precioboleto<0,0,t.precioboleto)else 0 end)*prod.porcentajecomision )/100),2) as comision_ordencompra,
                case when dev.porcentajemontodescuento>0 
                    then (sum(case when k.estatus =1 THEN IF( t.precioboleto<0,0,t.precioboleto)else 0 end))-(((sum(case when k.estatus =1 THEN IF( t.precioboleto<0,0,t.precioboleto)else 0 end))/100)* dev.porcentajemontodescuento)
                    else (sum(case when k.estatus =1 THEN IF( t.precioboleto<0,0,t.precioboleto)else 0 end))
                    end as venta_condescuento,
                case when  o.metodopago = 'card'
            then (case when dev.porcentajemontodescuento>0 
                    then (sum(case when k.estatus =1 THEN IF( t.precioboleto<0,0,t.precioboleto)else 0 end))-(((sum(case when k.estatus =1 THEN IF( t.precioboleto<0,0,t.precioboleto)else 0 end))/100)* dev.porcentajemontodescuento)
                    else (sum(case when k.estatus =1 THEN IF( t.precioboleto<0,0,t.precioboleto)else 0 end))
                    end)
            else 0
            end as ventas_tarjeta,
            case when o.metodopago = 'store'
            then (case when dev.porcentajemontodescuento>0 
                    then (sum(case when k.estatus =1 THEN IF( t.precioboleto<0,0,t.precioboleto)else 0 end))-(((sum(case when k.estatus =1 THEN IF( t.precioboleto<0,0,t.precioboleto)else 0 end))/100)* dev.porcentajemontodescuento)
                    else (sum(case when k.estatus =1 THEN IF( t.precioboleto<0,0,t.precioboleto)else 0 end))
                    end)
            else 0
            end as ventas_tienda
            from ordencompra o 
            inner join detalleordencompra d on o.idordercompra =d.idordercompra 
            inner join kardexboletos k on d.idkardexboletos = k.idkardexboletos 
            inner join tipoboleto t on k.idtipoboleto = t.idtipoboleto 
            left join ordencompradescuentos ocd ON o.idordercompra=ocd.idordercompra 
            left join descuentosevento dev on ocd.iddescuentoevento = dev.iddescuentoevento 
            inner join eventos e ON o.idevento =e.idevento 
            inner join usuarios prod ON e.idusualta = prod.idusuario 
            where o.estatus =1 {$where}
        GROUP  by o.idordercompra
    order by o.fechaalta ";
       
    return $this->getAll($query);
    
}

    public function findAddUrlEventoRP($url){
       $query = "SELECT rp.*, e.urlevento FROM eventos e
        LEFT JOIN relacionespublicas rp ON rp.idevento = e.idevento
        WHERE CONCAT(e.urlevento,rp.rpcomplementourl) LIKE '{$url}' 
        OR e.urlevento LIKE '{$url}'"; 
        
        return $this->getAll($query);
    }
    public function saveComplementoURLRP($idRelacionesPublicas, $complementourlrp){
         $query = "UPDATE relacionespublicas SET
                 rpcomplementourl = '{$complementourlrp}'
                 WHERE idrelacionespublicas = {$idRelacionesPublicas}";
        
        if ($this->executeQuery($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function mensajesEventoConfirmacion($idEvento){
        $query = "SELECT * FROM mensajeseventoconfirmacion WHERE idevento = {$idEvento} ORDER BY idmensajeevento DESC LIMIT 1"; 
        
        return $this->getAll($query);
    }
    public function changeStatusTipoBoleto($idTipoBoleto, $newStatus){
        $query = "UPDATE tipoboleto SET estatus = {$newStatus} WHERE idtipoboleto = {$idTipoBoleto}";
        
        if ($this->executeQuery($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
