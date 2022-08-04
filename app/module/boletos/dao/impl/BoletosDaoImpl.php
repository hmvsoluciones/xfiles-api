<?php

class BoletosDaoImpl extends Connection implements BoletosDao
{
    private $util;

    public function __construct()
    {
        $this->util = new UtilImpl();
    }

    public function generateFolio($idEvento)
    {
        $query = "SELECT COUNT(kb.idkardexboletos) + 1 AS asiento
        FROM kardexboletos kb
          INNER JOIN tipoboleto tb ON tb.idtipoboleto = kb.idtipoboleto
          WHERE tb.idevento = {$idEvento}";

        $response = $this->getAll($query);

        $asiento = "";
        $folio =  "";
        if ($response) {
            $asiento  = $response[0]["asiento"];
            $folio =  $idEvento."-".uniqid()."-".$asiento;
        }

        return array(
              "asiento" => $asiento,
              "folio" => $folio
          );
    }
    public function loadBoletosFisicos($numeroBoletosImpresos, $idUsuario, $idTipoBoleto)
    {
        $query = "INSERT INTO boletosfisicos
        (         
          numboletosimpresos,
          idusuario,          
          idtipoboleto,
          fecha
        )
        VALUES
        (            
          {$numeroBoletosImpresos},
          {$idUsuario},
          {$idTipoBoleto},
          curdate()
        );";

        if ($this->executeQuery($query)) {
            return $this->getLastInserId();
        } else {
            return false;
        }
    }
    public function loadKardexBoletos($idTipoBoleto, $folio, $asiento, $nombre=null, $correo=null, $telefono=null, $facebook=null, $instagram=null, $estatus)
    {
        $nombreItem = (!empty($nombre))?"'{$nombre}'": "NULL";
        $correoItem = (!empty($correo))?"'{$correo}'": "NULL";
        $telefonoItem = (!empty($telefono))?"'{$telefono}'": "NULL";
        $facebookItem = (!empty($facebook))?"'{$facebook}'": "NULL";
        $instagramItem = (!empty($instagram))?"'{$instagram}'": "NULL";
        
        $query = "INSERT INTO kardexboletos
        (          
          idtipoboleto,
          folioboletos,
          asiento,
          nombrepersona,
          correopersona,
          telefonopersona,
          facebookpersona,
          instagrampersona,
          estatus,
          ingreso          
        )
        VALUES
        (         
          {$idTipoBoleto},
          '{$folio}',
          {$asiento},
          {$nombreItem},
          {$correoItem},
          {$telefonoItem},
          {$facebookItem},
          {$instagramItem},
          {$estatus},
          0
        );";
        
        if ($this->executeQuery($query)) {
            return $this->getLastInserId();
        } else {
            return false;
        }
    }

    public function loadFisicoKardex($idBoletosFisicos, $idKardexBoletos)
    {
        $query = "INSERT INTO fisicokardex
        (          
          idboletosfisicos,
          idkardexboletos
        )
        VALUES
        (          
          {$idBoletosFisicos},
          {$idKardexBoletos}
        );
        
        ";
        
        if ($this->executeQuery($query)) {
            return $this->getLastInserId();
        } else {
            return false;
        }
    }
  
    public function loadCortesia($nombrePersonaCortesia, $correoPersonaCortesia, $telefonoPersonaCortesia, $numeroBoletosDisponibles, $motivo, $idTipoBoleto, $idUsuAlta)
    {
        $query = "INSERT INTO cortesias
        (
          nombrepersonacortesia,
          correopersonacortesia,
          telefonopersonacortesia,
          numeroboletosdisponibles,
          motivocortesia,
          idtipoboleto,
          idusuaalta,
          fechaalta,
          estatus
        )
        VALUES
        (
          '{$nombrePersonaCortesia}',
          '{$correoPersonaCortesia}',
          '{$telefonoPersonaCortesia}',
          {$numeroBoletosDisponibles},
          '{$motivo}',
          {$idTipoBoleto},
          {$idUsuAlta},
          curdate(),
          1
        );";
        
        
        if ($this->executeQuery($query)) {
            return $this->getLastInserId();
        } else {
            return false;
        }
    }
    public function loadCortesiaKardex($idCortesia, $idKardex)
    {
        $query = "INSERT INTO cortesiakardex
      (          
        idkardexboletos,
        idcortesia
      )
      VALUES
      (          
        {$idKardex},
        {$idCortesia}
      );
      ";
      
        if ($this->executeQuery($query)) {
            return $this->getLastInserId();
        } else {
            return false;
        }
    }
    
    public function loadListaInvitados($nombreInvitadoPrincipal, $telefonoInvitadoPrincipal, $correoInvitadoPrincipal, $numeroBoletosDisponibles, $idTipoBoleto,  $urlGenBoletos, $idUsuAlta)
    {
        $query = "INSERT INTO listainvitados
        ( nombreinvitadoprincipal,
          telefonoinvitadoprincipal,
          correoinvitadoprincipal,
          idtipoboleto,
          numeroboletosdisponibles,
          idusualta,
          fechaalta,
          estatus,
          urlgenboletos
        )
        VALUES
        (          
          '{$nombreInvitadoPrincipal}', 
          '{$telefonoInvitadoPrincipal}', 
          '{$correoInvitadoPrincipal}',  
           {$idTipoBoleto}, 
          {$numeroBoletosDisponibles}, 
          {$idUsuAlta},
          curdate(),
          1,
          '{$urlGenBoletos}'
          )";
          
        
        
        
        if ($this->executeQuery($query)) {
            return $this->getLastInserId();
        } else {
            return false;
        }
    }

    public function getBoletosPrecioPublico($idUsuario)
    {
        $comisionServicio = COMISION_FIJA_SERVICIO;

        $query = "SELECT porcentajecomision, {$comisionServicio} as comisionFija FROM  usuarios WHERE idusuario = {$idUsuario}";
        return $this->getAll($query);
    }

    public function getOrdenesCompraDeBoletosPendientes(){
        $query = "SELECT  DISTINCT  o.idordercompra FROM kardexboletos k 
        INNER JOIN detalleordencompra d ON d.idkardexboletos = k.idkardexboletos  
        INNER JOIN  ordencompra o ON o.idordercompra = d.idordercompra 
        WHERE k.estatus =2";

    return $this->getAll($query);
    }

    public function getBoletosFisicos($idTipoBoleto)
    {
        $query = "SELECT bf.*, u.correousu
        FROM boletosfisicos bf
        LEFT JOIN usuarios u ON u.idusuario = bf.idusuariopuntoventa
        WHERE bf.idtipoboleto = {$idTipoBoleto}";
       
        return $this->getAll($query);
    }

    public function getListaKardexBoletosFisicos($idTipoBoleto, $idBoletosFisicos)
    {
        $query = "SELECT kb.*, tb.tipoboleto as tipoboletodesc, e.nombreevento FROM kardexboletos kb
      INNER JOIN tipoboleto tb ON tb.idtipoboleto = kb.idtipoboleto
      INNER JOIN eventos e ON e.idevento = tb.idevento      
      INNER JOIN fisicokardex fk ON fk.idkardexboletos = kb.idkardexboletos AND fk.idboletosfisicos = {$idBoletosFisicos}
      INNER JOIN boletosfisicos bf ON bf.idboletosfisicos = fk.idboletosfisicos AND bf.idtipoboleto = {$idTipoBoleto}";
     
        return $this->getAll($query);
    }
    public function getListaKardexBoletosLista($idTipoBoleto, $idLista)
    {
    }
    
    public function getListaKardexBoletosCortesia($idTipoBoleto, $idCortesia)
    {
        $query ="SELECT kb.*, tb.tipoboleto as tipoboletodesc, e.nombreevento FROM kardexboletos kb
      INNER JOIN tipoboleto tb ON tb.idtipoboleto = kb.idtipoboleto
      INNER JOIN eventos e ON e.idevento = tb.idevento
      INNER JOIN cortesiakardex ck ON ck.idkardexboletos = kb.idkardexboletos AND ck.idcortesia = {$idCortesia}
      INNER JOIN cortesias c ON c.idcortesia = ck.idcortesia AND c.idtipoboleto = {$idTipoBoleto}";

        return $this->getAll($query);
    }
    
    public function loadListaInvitadosKardex($idListaInvitados, $idKardex)
    {
        $query = "INSERT INTO listakardex
      (          
        idlistainvitados,
        idkardexboletos
      )
      VALUES
      (          
        {$idListaInvitados},
        {$idKardex}
      );";
      
        if ($this->executeQuery($query)) {
            return $this->getLastInserId();
        } else {
            return false;
        }
    }
    public function getAllListaInvidatosByEvento($idEvento)
    {
        $query = " select l.idlistainvitados,l.nombreinvitadoprincipal,l.telefonoinvitadoprincipal,l.correoinvitadoprincipal,l.instagraminvitadoprincipal, "
    ." l.facebookinvitadoprincipal,t.tipoboleto ,l.numeroboletosdisponibles,l.horafingratuito,l.horafinmedioboleto,l.urlgenboletos from listainvitados l "
    ."inner join tipoboleto t ON t.idtipoboleto = l.idtipoboleto "
    ." WHERE t.idevento={$idEvento}";
   
        return $this->getAll($query);
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
    public function getBoletosCreadosTipoBoleto($idtipoBoleto){
         $query = "SELECT count(kb.idkardexboletos) as boletosTipo FROM kardexboletos kb where kb.idtipoboleto={$idtipoBoleto}";
    
        $result= $this->getAll($query);
        if($result && !empty($result[0]['boletosTipo'])){
            return $result[0]['boletosTipo'];
        }else{
            return 0;
        }
    }
    public function getCupoTipoBoleto($idtipoBoleto){
        $query = "SELECT cantidadboletos FROM tipoboleto WHERE idtipoboleto ={$idtipoBoleto}";
        $result= $this->getAll($query);
        if($result && !empty($result[0]['cantidadboletos'])){
            return $result[0]['cantidadboletos'];
        }else{
            return 0;
        }
    }

    public function getalltipoboletos($idEvento)
    {
        $query = "select t.idtipoboleto id, t.tipoboleto nombre from tipoboleto t where t.idevento ={$idEvento}";
 
        return $this->getAll($query);
    }

    //regresa maximo de boletos permitidos por tipo boleto
    public function getMaximoBoletos($idTipoBoleto)
    {
        $query = "select tb.cantidadboletos from tipoboleto tb where tb.idtipoboleto ={$idTipoBoleto}";
        $result =  $this->getAll($query);
        $maximo = (int)$result[0]['cantidadboletos'];
       
        return $maximo;
    }

    //regresa cantidad de boletos registraod en kardex boletos(boletos comprados y/o no disponibles)
    public function getBoletosInKardexByTipoBoleto($idTipoBoleto)
    {
        $query = "select count(*) as total from kardexboletos k where k.idtipoboleto = {$idTipoBoleto} AND k.estatus in(1,2)";

        $result =  $this->getAll($query);
        $total = (int)$result[0]['total'];
       
        return $total;
    }



    public function getallInicioEvento($idEvento)
    {
        $query = "select date_format(e.fechainicio,'%d/%m/%Y') as fechainicio, TIME_FORMAT(e.horainicio, '%r') as horainicio  from eventos e where e.idevento ={$idEvento}";

        return $this->getAll($query);
    }

    //Boletos vendidos detalles
    public function getBoletosVendidos($idEvento)
    {
         $query = "select count(t.tipoboleto) cantidad, t.tipoboleto from kardexboletos k join tipoboleto t on k.idtipoboleto =t.idtipoboleto where k.estatus=1 and  t.idevento={$idEvento} group by t.tipoboleto;";
         
        return $this->getAll($query);
    }
    //Boletos Escaneados detalles
    public function getBoletosEscaneados($idEvento)
    {
         $query = "select sum(if(k.ingreso in (1),1,0)) as escaneados, t.tipoboleto from kardexboletos k join tipoboleto t on k.idtipoboleto =t.idtipoboleto where  t.idevento={$idEvento} group by t.tipoboleto;";
         
        return $this->getAll($query);
    }

    public function getBoletosByIdListaInvitados($idListaInvitados)
    {
         $query = "SELECT DISTINCT kb.* FROM kardexboletos kb
        INNER JOIN listakardex lk ON lk.idkardexboletos = kb.idkardexboletos
        INNER JOIN listainvitados li ON li.idlistainvitados = lk.idlistainvitados
        WHERE li.idlistainvitados = {$idListaInvitados}";
        
        return $this->getAll($query);
    }

    
    public function getListaInvitadosByIdLista($idListaInvitados)
    {
        $query = "SELECT * FROM listainvitados WHERE idlistainvitados = {$idListaInvitados}";
        return $this->getAll($query);
    }
   
    public function getKardex($idKardex)
    {
        $query = "SELECT * FROM kardexboletos WHERE idkardexboletos = {$idKardex}";
        $data = $this->getAll($query);
        if ($data) {
            return $data[0];
        } else {
            return false;
        }
    }
    public function updateKardex($params)
    {
        $query = "UPDATE kardexboletos
                  SET         
                      nombrepersona = '{$params['nombrepersona']}',
                      correopersona = '{$params['correopersona']}',
                      telefonopersona = '{$params['telefonopersona']}',
                      facebookpersona = '{$params['facebookpersona']}',
                      instagrampersona = '{$params['instagrampersona']}'                      
              WHERE idkardexboletos = {$params['idkardexboletos']};";

        if ($this->executeQuery($query)) {
            return true;
        } else {
            return false;
        }
    }
    public function getIdListaKardexByKardex($idKardex)
    {
        $query = "SELECT idlistainvitados, idkardexlista FROM listakardex WHERE idkardexboletos = {$idKardex}";
        $response = $this->getAll($query);
        if ($response) {
            return $response[0];
        } else {
            return false;
        }
    }
    public function getFolioBoletoByKardex($idKardex)
    {
        $query = "SELECT folioboletos,idkardexboletos from kardexboletos WHERE idkardexboletos = {$idKardex}";
        
        $response = $this->getAll($query);
        if ($response) {
            return $response[0];
        } else {
            return false;
        }
    }
    public function getCortesias($idTipoBoleto)
    {
        $query = "SELECT * FROM cortesias where idtipoboleto = {$idTipoBoleto}";
        return $this->getAll($query);
    }
    public function getBoletoInfo($folioBoleto)
    {
         $query="SELECT kb.folioboletos,
          e.idevento,
          (SELECT doc.idordercompra FROM  detalleordencompra doc WHERE doc.idkardexboletos = kb.idkardexboletos) as idordencompra,
          (SELECT notas FROM mensajeseventoconfirmacion me WHERE me.idevento = e.idevento) as notasproductor,
          e.nombreevento,
          tb.tipoboleto,
          tb.tipoboletodesc,
          e.fechainicio,
          e.horainicio,
          tb.precioboleto,
          ue.direccionubicacionevento,
          ue.lugar,
          ue.referenciasubicacionevento,
          ue.notasubicacionevento,
          u.telefonousu as telefonoorganizador
      FROM kardexboletos kb
      INNER JOIN tipoboleto tb
            ON tb.idtipoboleto = kb.idtipoboleto
            AND kb.folioboletos = '{$folioBoleto}'
      INNER JOIN eventos e ON e.idevento = tb.idevento
      INNER JOIN ubicacionevento ue ON ue.idevento = e.idevento
      INNER JOIN usuarios u ON u.idusuario = e.idusuario";
    
    return $this->getAll($query);
  }
  public function getListaKardexOrdenCompra($idReferenciaPago) {
    $query = "SELECT kb.*, tb.tipoboleto as tipoboletodesc, e.nombreevento FROM kardexboletos kb
        INNER JOIN tipoboleto tb ON tb.idtipoboleto = kb.idtipoboleto
        INNER JOIN eventos e ON e.idevento = tb.idevento      
        INNER JOIN detalleordencompra doc ON doc.idkardexboletos = kb.idkardexboletos AND kb.estatus = 1
        INNER JOIN ordencompra oc ON oc.idordercompra = doc.idordercompra
        WHERE oc.idreferenciapago = '{$idReferenciaPago}'";
    
        return $this->getAll($query);
    }

    public function updateStatusKardexBoletosAfterPaying($idReferencia)
    {
        //ponemos estatus 1 si ya se pago
        $query = "UPDATE kardexboletos kab SET kab.estatus =1 WHERE kab.idkardexboletos IN (SELECT sc.idkardexboletos FROM (SELECT kb.idkardexboletos FROM kardexboletos kb
            INNER JOIN detalleordencompra doc ON doc.idkardexboletos = kb.idkardexboletos
            INNER JOIN ordencompra oc ON oc.idordercompra = doc.idordercompra
            WHERE oc.idreferenciapago = '{$idReferencia}') as sc)";

        if ($this->executeQuery($query)) {
            return true;
        } else {
            return false;
        }
    }

    public function updateStatusKardexBoletos($idReferencia, $estatus)
    {
        //ponemos estatus 1 si ya se pago, 2 apartado, -1 vencido
        $query = "UPDATE kardexboletos kab SET kab.estatus ={$estatus} WHERE kab.idkardexboletos IN (SELECT sc.idkardexboletos FROM (SELECT kb.idkardexboletos FROM kardexboletos kb
            INNER JOIN detalleordencompra doc ON doc.idkardexboletos = kb.idkardexboletos
            INNER JOIN ordencompra oc ON oc.idordercompra = doc.idordercompra
            WHERE oc.idreferenciapago = '{$idReferencia}') as sc)";

        if ($this->executeQuery($query)) {
            return true;
        } else {
            return false;
        }
    }
    public function getContactInfoToNotifyOnFullPayment($idReferenciaPago){
        $query="SELECT DISTINCT kb.correopersona, kb.telefonopersona, oc.idevento FROM ordencompra oc 
            INNER JOIN detalleordencompra doc ON doc.idordercompra = oc.idordercompra AND oc.idreferenciapago = '{$idReferenciaPago}'
            INNER JOIN kardexboletos kb ON kb.idkardexboletos = doc.idkardexboletos";
         
        return $this->getAll($query);
    }

    public function getIdTransaccionByOrdenCompra($idOrdenCompra) {
        $query = "SELECT idreferenciapago FROM ordencompra WHERE idordercompra = {$idOrdenCompra}";
        $response =  $this->getAll($query);
        if(count($response) > 0){
            return $response[0]["idreferenciapago"];
        } else {
            return FALSE;
        }

    }
    public function cancelarOrdenCompra($idOrdenCompra){
        $query = "UPDATE ordencompra SET estatus = -3, fechamodifica =now() WHERE idordercompra = {$idOrdenCompra};";
        if ($this->executeQuery($query)) {
            return true;
        } else {
            return false;
        }
    }

    public function cancelarKardexBoletosByOrdenCompra($idOrdenCompra){
        $query = "UPDATE kardexboletos SET estatus = -3, fechamodifica =now() WHERE idkardexboletos 
         IN(SELECT idkardexboletos FROM detalleordencompra WHERE idordercompra = {$idOrdenCompra});";
        if ($this->executeQuery($query)) {
            return true;
        } else {
            return false;
        }
    }
    public function esCortesiaOrListaInvitados($folioBoleto){
        $query = "SELECT 
        (SELECT count(*) from listakardex lk WHERE lk.idkardexboletos = kb.idkardexboletos) litainvitados,
        (SELECT count(*) from cortesiakardex ck WHERE ck.idkardexboletos = kb.idkardexboletos) cortesia
         FROM kardexboletos kb 
        WHERE kb.folioboletos = '{$folioBoleto}'";

        $response =  $this->getAll($query);
        if($response && count($response) > 0){
            return $response[0];
        } else {
            return FALSE;
        } 
    }
    public function getLogosProductorByIdEvento($idEvento){
        $query = "SELECT m.urlmultimediaget FROM multimedia m
            INNER JOIN usuariomultimedia um ON um.idmultimedia = m.idmultimedia
            INNER JOIN eventos e ON e.idusuario = um.idusuario AND e.idevento = {$idEvento}";

        return $this->getAll($query);
    }
}
