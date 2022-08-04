<?php

class MovilDaoImpl extends Connection implements MovilDao {

    private $util;

    function __construct(){
        $this->util = new UtilImpl();
    }

    public function getEventsForPuntoVentaUser($idUser) {
        
        $query = "SELECT e.idevento, e.nombreevento, e.idusuario, e.idcategoriaevento, e.fechainicio, e.fechafin, e.horainicio, e.horafin, e.estatus, e.idtipoevento, m.urlmultimediaget as header 
                    FROM eventos e
                    INNER JOIN boletosfisicos bf ON bf.idusuariopuntoventa = {$idUser}
                    INNER JOIN tipoboleto tb ON tb.idtipoboleto = bf.idtipoboleto AND e.idevento = tb.idevento
                    INNER JOIN usuarios u ON u.idusuario = bf.idusuariopuntoventa AND u.idtipousuario = 15 
                    INNER JOIN eventoestructuras ee ON ee.idevento = e.idevento AND ee.idtipoelemento = 4
                    INNER JOIN multimedia m ON m.idmultimedia = ee.idmultimedia
                    WHERE e.fechafin >= curdate()";
        
        return $this->getAll($query);           
    }
    public function getEventsForAppMovilUser($idUser){
        $query = "SELECT e.idevento, e.nombreevento, e.idusuario, e.idcategoriaevento, e.fechainicio, e.fechafin, e.horainicio, e.horafin, e.estatus, e.idtipoevento, m.urlmultimediaget as header FROM eventos e
            INNER JOIN usuariosapp ua ON ua.idusuario = {$idUser} AND e.idevento = ua.idevento
            INNER JOIN usuarios u ON u.idusuario = ua.idusuario AND u.idtipousuario = 3
            INNER JOIN eventoestructuras ee ON ee.idevento = e.idevento AND ee.idtipoelemento = 4
            INNER JOIN multimedia m ON m.idmultimedia = ee.idmultimedia
            WHERE e.fechafin >= curdate()";
        
        return $this->getAll($query);
    }

    public function esBoletoFisicoValido($idEvento, $idUsuario, $folioBoleto) {
        $query = "SELECT kb.*, e.idevento FROM boletosfisicos bf  
            INNER JOIN fisicokardex fk ON fk.idboletosfisicos = bf.idboletosfisicos AND bf.idusuariopuntoventa = {$idUsuario}
            INNER JOIN tipoboleto tb ON tb.idtipoboleto = bf.idtipoboleto
            INNER JOIN eventos e ON e.idevento = tb.idevento AND e.idevento = {$idEvento}
            INNER JOIN kardexboletos kb ON kb.idkardexboletos = fk.idkardexboletos AND kb.folioboletos = '{$folioBoleto}'";
        return $this->getAll($query);
    }

    public function esBoletoPaseEventoValido($idEvento, $idUsuario, $folioBoleto) {
        $query = "SELECT kb.*,e.idevento, e.fechainicio, e.horafinboletogratis FROM kardexboletos kb
            INNER JOIN tipoboleto tb ON tb.idtipoboleto = kb.idtipoboleto AND kb.folioboletos = '$folioBoleto' AND kb.estatus NOT IN(3,-3)
            INNER JOIN eventos e ON e.idevento = tb.idevento AND e.idevento = {$idEvento}";
        return $this->getAll($query);
    }

    public function activarBoletoFisico($folioBoleto) {
        $query = "UPDATE kardexboletos SET estatus = 1 WHERE folioboletos = '$folioBoleto';";
        
        return $this->executeQuery($query);
    }

    public function paseEvento($folioBoleto) {
        $query = "UPDATE kardexboletos SET ingreso = 1 WHERE folioboletos = '$folioBoleto';";
        
        return $this->executeQuery($query);
    }
    public function esCortesiaByFolioBoleto($folioBoleto) {

        $query = "SELECT k.idkardexboletos FROM cortesiakardex c 
            INNER JOIN kardexboletos k ON k.idkardexboletos = c.idkardexboletos 
            WHERE k.folioboletos = '{$folioBoleto}'";

        if($this->getAll($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function esListaInvitadosByFolioBoleto($folioBoleto){
        $query = "SELECT c.idkardexboletos FROM listakardex c 
        INNER JOIN kardexboletos k ON k.idkardexboletos = c.idkardexboletos 
        WHERE k.folioboletos = '{$folioBoleto}'";

        if($this->getAll($query)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public function paseEventoConMedioBoleto($folioBoleto) {
        $query = "UPDATE kardexboletos SET ingreso = 2 WHERE folioboletos = '$folioBoleto';";
        
        return $this->executeQuery($query);
    }

    public function esValidoPagoSinMedioBoleto($fechaEvento, $horaPagoMedioBoletoEvento){
        
        $fechaHoraEvento = date("{$fechaEvento} {$horaPagoMedioBoletoEvento}");
        $fechaHoraActual = date("Y-m-d H:i:s");

        if(strtotime($fechaHoraEvento) > strtotime($fechaHoraActual)){
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getKardexByFolioBoleto($folioBoleto) {
        $query = "SELECT kb.* FROM kardexboletos kb WHERE kb.folioboletos = '{$folioBoleto}'";
        return $this->getAll($query);
    }

    public function activarBoletoFisicoGlobal($folioBoleto, $idUsuarioActiva) {
        $query = "UPDATE kardexboletos SET estatus = 1, idusuario_activar={$idUsuarioActiva} WHERE folioboletos = '$folioBoleto';";
        
        return $this->executeQuery($query);
    }

    public function paseEventoGlobal($folioBoleto, $idUsuarioIngreso) {
        $query = "UPDATE kardexboletos SET ingreso = 1, idusuario_ingreso={$idUsuarioIngreso} WHERE folioboletos = '$folioBoleto';";
        
        return $this->executeQuery($query);
    }
    public function paseEventoConMedioBoletoGlobal($folioBoleto, $idUsuarioIngreso) {
        $query = "UPDATE kardexboletos SET ingreso = 2,idusuario_ingreso={$idUsuarioIngreso} WHERE folioboletos = '$folioBoleto';";
        
        return $this->executeQuery($query);
    }
    public function getFechaHoraFinListaCortesia($folioBoleto){
        $query = "SELECT e.horafinboletogratis, e.fechainicio FROM kardexboletos k
        INNER JOIN tipoboleto tb ON tb.idtipoboleto = k.idtipoboleto
        INNER JOIN eventos e ON e.idevento = tb.idevento
        WHERE k.folioboletos = '{$folioBoleto}'";

        return $this->getAll($query);
    }
    
}
