<?php


define("REPORTES_BY_EVENTO", 
    array(
        "REPORTE_GENERAL_VENTAS" => "SELECT 
            e.nombreevento as 'Nombre evento',      
            concat(u.nombreusu,' ', u.appusu, ' ', u.ampusu) as 'Nombre de productor',
            k.folioboletos as 'Folio del boleto',
            t.tipoboleto as 'Tipo boleto',
            t.precioboleto as 'Precio base boleto',
            k.nombrepersona as 'Nombre persona compra', 
            k.correopersona as 'Correo persona compra',
            k.telefonopersona as 'Telefono persona compra',
            o.idordercompra as 'Orden de compra',
            o.idreferenciapago as 'Id de pago OpenPay',
            CASE k.estatus
                WHEN 1 THEN 'Pendiente'
                WHEN 2 THEN 'Pagado'
                When 3 THEN 'Vencido'
                ELSE 'Desconocido'
            END as 'Estatus de pago',
            o.rp as 'Relación publica',
            CASE
            WHEN EXISTS (SELECT idkardexboletos
                        FROM listakardex lk
                        WHERE lk.idkardexboletos = k.idkardexboletos) THEN 'Lista Invitado'
            WHEN EXISTS (SELECT idkardexboletos
                        FROM cortesiakardex ck
                        WHERE ck.idkardexboletos = k.idkardexboletos) THEN 'Cortesia'
            WHEN EXISTS (SELECT idkardexboletos
                        FROM fisicokardex gk
                        WHERE gk.idkardexboletos = k.idkardexboletos) THEN 'Boleto fisico'
            WHEN EXISTS (SELECT idkardexboletos
                        FROM detalleordencompra d
                        WHERE d.idkardexboletos = k.idkardexboletos) THEN
                CASE
                WHEN EXISTS (SELECT *
                            FROM ordencompra o2
                            WHERE o2.idordercompra = o.idordercompra
                            AND   o2.rp <> '') THEN 'Compra relaciones publicas '
                ELSE 'Compra online'
                END 
            ELSE 'NO'
            END AS 'Tipo compra'
        FROM kardexboletos k
        INNER JOIN tipoboleto t ON k.idtipoboleto = t.idtipoboleto
        INNER JOIN eventos e ON t.idevento = e.idevento AND e.idevento = ?
        INNER JOIN usuarios u ON e.idusualta = u.idusuario LEFT
        JOIN detalleordencompra do ON do.idkardexboletos = k.idkardexboletos
        LEFT JOIN ordencompra o ON o.idordercompra = do.idordercompra",
        
        "REPORTE_PUNTOS_VENTA" => "SELECT t.tipoboleto, b.numboletosimpresos,  
        COUNT(CASE WHEN EXISTS (SELECT idkardexboletos FROM fisicokardex gk WHERE gk.idkardexboletos = k.idkardexboletos and k.estatus=3) THEN 1 END) AS 'Boletos sin venta',
        COUNT(CASE WHEN EXISTS (SELECT idkardexboletos FROM fisicokardex gk WHERE gk.idkardexboletos = k.idkardexboletos and k.estatus=1) THEN 1 END) AS 'Boletos vendidos'
        FROM boletosfisicos b 
        INNER JOIN usuarios u ON b.idusuariopuntoventa = u.idusuario 
        INNER JOIN tipoboleto t ON b.idtipoboleto = t.idtipoboleto 
        INNER JOIN eventos e ON t.idevento = e.idevento AND e.idevento = ?
        INNER JOIN fisicokardex f ON f.idboletosfisicos = b.idboletosfisicos 
        INNER JOIN kardexboletos k ON f.idkardexboletos =k.idkardexboletos", 

        "REPORTE_VENTAS_TOTALES" =>"SELECT t.tipoboleto AS 'Tipo boleto',COUNT(k.idtipoboleto) AS 'Boletos vendidos' ,((t.cantidadboletos )- COUNT(k.idtipoboleto)) AS 'Disponibles', 
        SUM(t.precioboleto) AS 'Ingesos por tipo boleto',
        (COUNT(k.idtipoboleto)-(
        	COUNT(CASE WHEN EXISTS (SELECT idkardexboletos FROM listakardex lk WHERE lk.idkardexboletos = k.idkardexboletos) THEN 1 END ) +
        COUNT(CASE WHEN EXISTS (SELECT idkardexboletos FROM cortesiakardex ck WHERE ck.idkardexboletos = k.idkardexboletos) THEN 1 END ) +
        COUNT(CASE WHEN EXISTS (SELECT idkardexboletos FROM fisicokardex gk WHERE gk.idkardexboletos = k.idkardexboletos) THEN 1 END) 
        )) AS 'Boletos online',
        COUNT(CASE WHEN EXISTS (SELECT idkardexboletos FROM listakardex lk WHERE lk.idkardexboletos = k.idkardexboletos) THEN 1 END ) AS 'Boletos lista invitados',
        COUNT(CASE WHEN EXISTS (SELECT idkardexboletos FROM cortesiakardex ck WHERE ck.idkardexboletos = k.idkardexboletos) THEN 1 END ) as 'Boletos cortesias',
        COUNT(CASE WHEN EXISTS (SELECT idkardexboletos FROM fisicokardex gk WHERE gk.idkardexboletos = k.idkardexboletos) THEN 1 END) AS 'Boletos puntos de venta'
        FROM kardexboletos k 
        INNER JOIN tipoboleto t ON k.idtipoboleto = t.idtipoboleto 
        INNER JOIN eventos e ON t.idevento = e.idevento AND e.idevento = ?
        GROUP BY k.idtipoboleto "
        
        )
);
