<?php

class ReportsDaoImpl extends Connection implements ReportsDao
{
    private $util;

    public function __construct()
    {
        $this->util = new UtilImpl();
    }
    

    public function getBoletosVendidos($id)
    {
        $query = "select k.idkardexboletos,k.idtipoboleto,k.folioboletos,k.nombrepersona,k.correopersona,k.telefonopersona,k.asiento,k.estatus, "
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
        ."INNER JOIN usuarios u on e.idusualta = u.idusuario "
        ."LEFT JOIN detalleordencompra do on  do.idkardexboletos = k.idkardexboletos "
        ."LEFT JOIN ordencompra o on o.idordercompra =do.idordercompra";
        
        
        //return $this->getAll($query);
        return $this->getHTMLTable($query);
    }
    
    public function getHTMLTableReport($sql)
    {
        return $this->getHTMLTable($sql);
    }

    public function serchOrdenCompra($ordenCompra){
        $query = "SELECT oc.idordercompra,
                        e.nombreevento,
                        oc.numboletoscompra,
                        oc.totalcompra,
                        oc.idevento,
                        oc.idreferenciapago,
                        oc.fechaalta
                FROM ordencompra oc
                INNER JOIN eventos e
                        ON e.idevento = oc.idevento
                        AND idordercompra = {$ordenCompra}";

        return $this->getAll($query);
    }
    
}
