<?php

class PaymentServiceImpl implements PaymentService
{
    private $paymentDao;
    private $openPayDao;
    private $eventDao;
    private $util;
    private $carteraDao;
    private $boletosService;
    private $usersDao;

    public function __construct()
    {
        $this->paymentDao = new PaymentDaoImpl();
        $this->openPayDao = new OpenPayDaoImpl();
        $this->boletosDao = new BoletosDaoImpl();
        $this->eventDao = new EventsDaoImpl();
        $this->carteraDao = new CarteraDaoImpl();
        $this->boletosService = new BoletosServiceImpl();
        $this->usersDao = new UsersDaoImpl();
        $this->util = new UtilImpl();
    }

    public function loadKardexBoletos($idEvento, $idTipoBoleto, $cantidadBoletos, $nombre, $correo, $telefono, $facebook, $instagram, $estatus)
    {
        $listaIdKardex = array();
        for ($i = 0; $i < $cantidadBoletos; $i++) {
            $folios = $this->boletosDao->generateFolio($idEvento);

            $inserted = $this->boletosDao->loadKardexBoletos($idTipoBoleto, $folios["folio"], $folios["asiento"], $nombre, $correo, $telefono, $facebook, $instagram, $estatus);

            array_push($listaIdKardex, $inserted);
        }
        return $listaIdKardex;
    }

    /**
     * Regresa el monto neto que se debe pagar por la cantidad de cierto tipos boletos
     */
    public function calcularMontoNetoPorTipoBoleto($idEvento, $idTipoBoleto, $cantidadBoletos)
    {
        $tiposBoleto = $this->eventDao->getAllTipoBoletoByEvento($idEvento);
      
        foreach ($tiposBoleto as $key => $tipoBoleto) {
            if ($tipoBoleto["idtipoboleto"] == $idTipoBoleto) {
                $costo = $tipoBoleto["precioboleto"];
            }
        }
        
        return $costo * $cantidadBoletos;
    }

    /**
     * Regresa el cupon si es que existe la clave o null
     * Recibe un arreglo params con indices "evento" y "cupon"
     */
    public function validarCupon($params)
    {
        $response = new WSResponse();

        $idEvento = $params["evento"];
        $claveCupon =  $params["cupon"];
        $cupones = $this->eventDao->getAllDescuentosEventoByEvento($idEvento);

      

        $cuponResponse =null;
        foreach ($cupones as $key => $cupon) {
            if ($cupon["clavecupon"] == $claveCupon) {
                $cuponResponse = $cupon;
            }
        }

        
       

        if ($cuponResponse) {
            $response->setSuccess(true);
            $response->setMessage("Cupon aplicado correctamente");
            $response->setObject($cuponResponse);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Cupon invalido");
            $response->setObject(null);
        }
        
        return $response->expose();
    }


    public function createOrden($params)
    {
        $response = new WSResponse();

        try {
            if(!isset($params["puntos"])){
                $params["puntos"] = 0;
            }
            $this->paymentDao->beginTransaction();

            //Verificamos si usa cupon y validamos, el objeto será null si el cupon es invalido;
            $cupon = $this->validarCupon($params);

            //Calculamos el monto neto
            $montoCalculado = 0;
            foreach ($params['boletos'] as $key => $boleto) {
                $montoCalculado += $this->calcularMontoNetoPorTipoBoleto($params["evento"], $boleto["idTipoBoleto"], $boleto["cantidad"]);
            }

            //Calculamos comision
            $evento = $this->eventDao->get($params["evento"]);
            $usuarioProductor = $this->usersDao->get($evento[0]['idusuario']);
            $comisionUsuario = (int)$usuarioProductor[0]['porcentajecomision'];
            $comisionServicio = (int) COMISION_FIJA_SERVICIO;
            $comisionUsuarioCalculada = $montoCalculado * ($comisionUsuario/100);

            //sumamos comision
            $montoCalculado += $comisionUsuarioCalculada + $comisionServicio;

            //Validamos cupon y descontamos
            $porcentajeDescuento = 0;
            if ($cupon["object"] && $cupon["object"] ["porcentajemontodescuento"]) {
                $porcentajeDescuento =$cupon["object"] ["porcentajemontodescuento"];
            }

            $porcentajeDescuento;
            $cantidadADescontar = $montoCalculado * ($porcentajeDescuento/100);
            $montoCalculado =  $montoCalculado - $cantidadADescontar;


            //verificamos la cantidad de boletos a comprar:
           
            $params['numboletoscompra'] = 0;
            foreach ($params['boletos'] as $key => $value) {
                $params['numboletoscompra'] += $value["cantidad"];
            }


            //Validamos si se usan puntos y si tiene los suficientes
            // Definimos los puntos y validamos si tiene los suficientes
            $puntosAusar =  $params["puntos"];
            if ($puntosAusar>0) {
                
                $puntosReales = $this->carteraDao->getPuntos($params["usuario"])[0]['puntosdisponibles'];
                
                if ($puntosReales<$puntosAusar) {
                    throw new Exception("No cuenta con suficientes puntos para realizar la acción");
                } else {
                    //quitamos puntos de cartera
                    $paramsToQuitarPuntos  = array(
                        "usuario" => $params["usuario"],
                        "monto" => $puntosAusar);

                    $this->quitarPuntosCartera($paramsToQuitarPuntos);
                }
            }

            $montoCalculado = $montoCalculado - $puntosAusar;

            // Generamos orden de compra sin el id de transacción, con estatus 2, creado (pendiente)
            //El orden de id son:
            // 1 Pagado
            // 2 Creado/pendiente
            //-3
            // -1 Otro/Vencido
            // -2 invalido cuando no paso el pago
            $params["estatus"] = 2;

           
            if ($params["rp"] != null &&  $params["rp"] != "" && $params['metodopago'] == "cash") {
                $params["estatus"] = 1;
                $params['totalcompra'] = $montoCalculado;
            }
            
            if(  $params['totalcompra'] != $montoCalculado){
                throw new Exception("El monto enviado y el calculado no cuadra");
            }

            $params['idreferenciapago'] = null;
            $idOrdenCompra = $this->paymentDao->loadOrdenCompra($params);

            if (!$idOrdenCompra) {
                throw new Exception("No se logro crear la orden de compra");
            }

            $descuentoId = 0;
            if ($cupon["object"] && $cupon["object"] ["iddescuentoevento"]) {
                $descuentoId =$cupon["object"] ["iddescuentoevento"];
                $this->paymentDao->saveOrdenCompraDescuento($descuentoId, $idOrdenCompra);
            }

            //creamos los boletos (kardexBoletos)
            //Aquí se está enviando un objeto on el tipo de boleto y la cantidad a crear
            $boletosCreados = array();
            foreach ($params['boletos'] as $key => $value) {
                $boletosCreados = array_merge($boletosCreados, $this->loadKardexBoletos($params["evento"], $value["idTipoBoleto"], $value["cantidad"], null, $params["correo"], $params["telefono"], null, null, $params["estatus"]));
            }

            if (count($boletosCreados) <= 0) {
                throw new Exception('No se lograron crear los boletos');
            }

         

            //creamos el detalleOrdenCompra (dupla de kardex con ordencompra)
            $paramsDor["idordercompra"] = $idOrdenCompra;
            foreach ($boletosCreados as $key => $idKardexBoletos) {
                $paramsDor["idkardexboletos"] = $idKardexBoletos;
                $adOrdenCompra = $this->paymentDao->loadDetalleOrdenCompra($paramsDor);

                if (!$adOrdenCompra) {
                    throw new Exception('No se lograron ligar los boletos');
                }
            }

            $responseObj =  array(
                "ordencompra" => $idOrdenCompra,
            );
        
            $message = "Registro guardado correctamente";
            //ponemos idORdenCompra en idreferencia si es en efectivo y enviamps correo
            if ($params["rp"] != null &&  $params["rp"] != "" && $params['metodopago'] == "cash") {
                $params['idreferenciapago'] = $idOrdenCompra;
                $params['idordercompra'] = $idOrdenCompra; 
                $this->paymentDao->updateIdReferenciaOrdenCompra($params);
                
                $this->paymentDao->commitTransaction();

                $enviado = $this->enviarCorreoBoletos($idOrdenCompra);
                $message = "Registro RP guardado correctamente orden: {$idOrdenCompra}, correo enviado: {$enviado}";
            }

            if ($idOrdenCompra) {
                $response->setSuccess(true);
                $response->setMessage($message);
                $response->setObject($responseObj);
                try{
                $this->paymentDao->commitTransaction();
                }catch(Exception $e){}
            } else {
                $response->setSuccess(false);
                $response->setMessage("Ocurrio un error al registrar la solicitud de pago");
                $response->setObject(null);
                $this->paymentDao->rollBackTransaction();
            }
        } catch (Exception $e) {
            $this->paymentDao->rollBackTransaction();
            $response->setSuccess(false);
            $response->setMessage("Ocurrio una excepcion al registrar la orden de compra: " . $e->getMessage());
            $response->setObject(null);
        }

        return $response->expose();
    }
    public function retrieveOrden($params){
        $response = new WSResponse();

        try {
            $this->paymentDao->beginTransaction();
            $idOrdenCompra = $params['idOrdenCompra'];

           $detalleOrdenCompra = $this->paymentDao->retrieveDetalleDeOrdenCompra($idOrdenCompra);

          
           $responseObj =  array(
            "tickets" => $detalleOrdenCompra,
        );

            if ($detalleOrdenCompra) {
                $response->setSuccess(true);
                $response->setMessage("Registro obtenido correctamente");
                $response->setObject($responseObj);
                $this->paymentDao->commitTransaction();
            } else {
                $response->setSuccess(false);
                $response->setMessage("Ocurrio un error al obtener la orden de compra");
                $response->setObject(null);
                $this->paymentDao->rollBackTransaction();
            }
        } catch (Exception $e) {
            $this->paymentDao->rollBackTransaction();
            $response->setSuccess(false);
            $response->setMessage("Ocurrio una excepcion al obtener la orden de compra: " . $e->getMessage());
            $response->setObject(null);
        }

        return $response->expose();

    }

    public function pagarOrden1($params)
    {
        $response = new WSResponse();
        $cargo = new CargoDTO();
        $config = parse_ini_file(__DIR__ . '../../../../../config/openpay.ini');
        $idOrdenCompra = $params["ordenCompra"];
    
        try {
            $this->paymentDao->beginTransaction();

            
            

            //Objeto customer para el cargo a OpenPay:
            $customer = array(
                "name" => $params["cliente"]["nombre"],
                "last_name" => $params["cliente"]["apellidos"],
                "phone_number" => $params["cliente"]["celular"],
                "email" => $params["cliente"]["correo"]
            );
                        

            
                /*$chargeRequest = array(
                    "method" =>"card",
                    "source_id" => $params["source_id"],
                    "device_session_id" => $params["deviceSessionId"],
                    'amount' => 20,
                    'description' => " name|event| ",
                    'customer' => $customer,
                    'send_email' => false,
                    'confirm' => false,
                    //'due_date' => date(DateTime::ISO8601, strtotime('  +20 minute')),
                    //'use_3d_secure' => "true",
                    //'redirect_url' => $urlRedirect 
                );*/
                $customer = array(
                    'name' => 'Juan',
                    'last_name' => 'Vazquez Juarez',
                    'phone_number' => '4423456723',
                    'email' => 'juan.vazquez@empresa.com.mx'
                );
            
                $chargeRequest = array(
                    'method' => 'card',
                    'source_id' => $params["source_id"],
                    'amount' => 100,
                    'currency' => 'MXN',
                    'description' => 'Cargo inicial a mi merchant',
                    //'order_id' => 'oid-00052',
                    'device_session_id' => $params["deviceSessionId"],
                    'customer' => $customer,
                    //OK
                    'use_3d_secure' => "true",
                    'redirect_url' => "https://ticketapp.live"
            
                );
            
            
            $payResponse = $this->openPayDao->makeAPay($chargeRequest);
           var_dump($payResponse);
           exit;
          

           
        } catch (Exception $e) {
            var_dump($e);
           exit;
          
            
        }

       
    }

    public function pagarOrden($params)
    {
        $response = new WSResponse();
        $cargo = new CargoDTO();
        $config = parse_ini_file(__DIR__ . '../../../../../config/openpay.ini');
        $idOrdenCompra = $params["ordenCompra"];
        $isPagoEnAbonos = $params['isAbonos'];
    
        try {
            $this->paymentDao->beginTransaction();

            $ordenCompra = $this->paymentDao->getOrdenCompra($idOrdenCompra)[0];
            $montoAPagar = $ordenCompra["totalcompra"];

            if($params["meses"] == 6){
                $montoAPagar = $montoAPagar + ($montoAPagar * 0.107);
            } else if($params["meses"] == 12){
                $montoAPagar = $montoAPagar + ($montoAPagar * 0.167);
            }
            
            $evento = $this->eventDao->get($ordenCompra["idevento"])[0] ;
            

            // Definimos los puntos y validamos si tiene los suficientes
            $puntosAusar =  $params["puntos"];
            if ($puntosAusar>0) {
                $puntosReales = $this->carteraDao->getPuntos($params["usuario"])[0]['puntosdisponibles'];
                if ($puntosReales<$puntosAusar) {
                    throw new Exception("No cuenta con suficientes puntos para realizar la acción");
                } else {
                    //quitamos puntos de cartera
                    $paramsToQuitarPuntos  = array(
                        "usuario" => $params["usuario"],
                        "monto" => $puntosAusar);

                    $this->quitarPuntosCartera($paramsToQuitarPuntos);
                }
            }

            $montoCalculado = $montoAPagar - $puntosAusar;

            if ($isPagoEnAbonos == "true") {
                $montoCalculado =   $montoCalculado/4;
            }          
            
            //creamos el objeto cargo hacía OpenPay
            $cargo->setCurrency =  "MXN";

            //Objeto customer para el cargo a OpenPay:
            $customer = array(
                'name' => $params["cliente"]["nombre"],
                'last_name' => $params["cliente"]["apellidos"],
                'phone_number' => $params["cliente"]["celular"],
                'email' => $params["cliente"]["correo"]
            );
                        

            $urlRedirect = "";
            $monto = $montoCalculado;
           
            $isPagoEnAbonos = $params['meses']==0? true: false;
            if ($isPagoEnAbonos == true) {
                $urlRedirect = URL_BASE_PAGOS.'event/success-abono.php';
                $monto = $monto * PORCENTAJE_INICIAL_ABONOS;
            } else {
                $urlRedirect =  URL_BASE_PAGOS.'event/success.php';
            }
            $chargeRequest=null;

           
        
            $monto = round($monto, 2);
           $payments=null;
            if($params['meses'] >1){
                $payments = array("payments" => $params['meses'] ); 
            }
             

            if ($params["metodopago"] == "store") {
                $chargeRequest = array(
                    "method" =>'store',
                    'amount' => $monto,
                    'description' => $evento['nombreevento']." | ".$idOrdenCompra,
                    'customer' => $customer,
                    'send_email' => false,
                    'confirm' => false,
                    'due_date' => date(DateTime::ISO8601, strtotime(' +1 day')),
                    'redirect_url' => $urlRedirect )
                ;
            } else if($params["metodopago"] == "card") {
                $chargeRequest = array(
                    'method' => 'card',
                    'source_id' => $params["source_id"],
                    'payment_plan' => $payments,
                    'amount' => $monto,
                    'currency' => 'MXN',
                    'description' => $evento['nombreevento']." | ".$idOrdenCompra,
                    'device_session_id' => $params["deviceSessionId"],
                    'customer' => $customer,
                    //OK
                    'use_3d_secure' => "true",
                    'redirect_url' =>  $urlRedirect
            
                );
            }
            /*
            $logFile = fopen("openpaylog.txt", 'a') or die("Error creando archivo");
            fwrite($logFile, "\n".date("d/m/Y H:i:s").json_encode($chargeRequest)) or die("Error escribiendo en el archivo");fclose($logFile);
            */
            $payResponse = $this->openPayDao->makeAPay($chargeRequest);
           
           

            if (!isset($payResponse) || $payResponse->id==null || $payResponse->id == "") {
                throw new Exception("No fue posible crear el proceso de pago en OpenPay:".$payResponse );
            }
            

            $params["idreferenciapago"] = $payResponse->id;
            $params["idordercompra"] = $idOrdenCompra;
            
            $params["estatus"] = 2;
            if ($isPagoEnAbonos) {
                $params["estatus"] = 10;
            }
           


            // $this->paymentDao->beginTransaction();
            //actualizamos la referencia de pago de la orden de compra
           $this->paymentDao->updateIdReferenciaAndMethodOrdenCompra($params);
           $this->paymentDao->updateDatosUsuarioInKardexAndEstatus($params);
            
            

            $isAbonoRegistryCompleted = false;
            if ($isPagoEnAbonos) {
                $isAbonoRegistryCompleted = $this->paymentDao->agregarAbonoPago($idOrdenCompra, $payResponse->id, $monto, 0);
            }

            if ($isPagoEnAbonos == "true" && !$isAbonoRegistryCompleted) {
                throw new Exception("No se logro crear el abono de la compra");
            }

            //validamos si es pago en comercio o en linea
            if ($payResponse->payment_method->type =="store") {
                $evento = "evento prueba";
                $monto =  $params["totalcompra"];
                //$orden =  $paramsDor["idordercompra"];
               // $code = $payResponse->payment_method->barcode_url;
                $refcode= $payResponse->payment_method->reference;
                

                $paymentPath ="";
                if(PAYMENT_MODE == "dev"){
                    $paymentPath = DASHBOARD_PATH_DEV;
                } else {
                    $paymentPath = DASHBOARD_PATH_PRODUCTION;
                }
                $urlPDF = $paymentPath.$config["id"]."/".$refcode;
                
                $responseObj =  array(
                    "payI" => $payResponse->id,
                    "method" => $payResponse->payment_method,
                   //"ordencompra" => $paramsDor["idordercompra"],
                    "urlPagoEnComercio" =>  $urlPDF
                );
                try{$this->paymentDao->commitTransaction();}catch(Exception $e){ }
                $this->enviarCorreoBoletosPDF($params["idreferenciapago"], $urlPDF);
            } else {
                $responseObj =  array(
                    "payI" => $payResponse->id,
                    "method" => $payResponse->payment_method,
                    "card" => $payResponse ->card,
                    "ordencompra" => $idOrdenCompra
                );
            }

           

            if ($payResponse) {
                $response->setSuccess(true);
                $response->setMessage("Registro guardado correctamente");
                $response->setObject($responseObj);
                try{$this->paymentDao->commitTransaction();}catch(Exception $e){ }
            } else {
                $response->setSuccess(false);
                $response->setMessage("Ocurrio un error al registrar la solicitud de pago");
                $response->setObject(null);
                $this->paymentDao->rollBackTransaction();
            }
        } catch (Exception $e) {
            $this->paymentDao->rollBackTransaction();
            $response->setSuccess(false);
            $response->setMessage("Ocurrio una excepcion al registrar la orden de compra payment: " . $e->getMessage());
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function payment($params)
    {
        $response = new WSResponse();
        $cargo = new CargoDTO();
        $config = parse_ini_file(__DIR__ . '../../../../../config/openpay.ini');
        $idEvento = $params["evento"];
        $evento = $this->eventDao->get($idEvento)[0];

        try {
            $this->paymentDao->beginTransaction();

            //nombre del cliente
            $nombreCliente =  $params["cliente"]["nombre"]. " ". $params["cliente"]["apellidos"];

            //Verificamos si usa cupon y validamos, el objeto será null si el cupon es invalido;
            $cupon = $this->validarCupon($params);

            //validamos si es pago parcial
            $isPagoEnAbonos =  $params["isAbonos"];

            //Calculamos el monto neto
            $montoCalculado = 0;
            foreach ($params['boletos'] as $key => $boleto) {
                $montoCalculado += $this->calcularMontoNetoPorTipoBoleto($params["evento"], $boleto["tipoboleto"], $boleto["cantidad"]);
            }

            //Calculamos comision
            $evento = $this->eventDao->get($params["evento"]);
            $usuarioProductor = $this->usersDao->get($evento[0]['idusuario']);
            $comisionUsuario = (int)$usuarioProductor[0]['porcentajecomision'];
            $comisionServicio = (int) COMISION_FIJA_SERVICIO;
            $comisionUsuarioCalculada = $montoCalculado * ($comisionUsuario/100);

            //sumamos comision
            $montoCalculado += $comisionUsuarioCalculada + $comisionServicio;


            //Validamos cupon y descontamos
            $porcentajeDescuento = 0;
            if ($cupon["object"] && $cupon["object"] ["porcentajemontodescuento"]) {
                $porcentajeDescuento =$cupon["object"] ["porcentajemontodescuento"];
            }
            $porcentajeDescuento;
            $cantidadADescontar = $montoCalculado * ($porcentajeDescuento/100);
            $montoCalculado =  $montoCalculado - $cantidadADescontar;


            // Definimos los puntos y validamos si tiene los suficientes
            $puntosAusar =  $params["puntos"];
            if ($puntosAusar>0) {
                $puntosReales = $this->carteraDao->getPuntos($params["usuario"])[0]['puntosdisponibles'];
                if ($puntosReales<$puntosAusar) {
                    throw new Exception("No cuenta con suficientes puntos para realizar la acción");
                } else {
                    //quitamos puntos de cartera
                    $paramsToQuitarPuntos  = array(
                        "usuario" => $params["usuario"],
                        "monto" => $puntosAusar);

                    $this->quitarPuntosCartera($paramsToQuitarPuntos);
                }
            }

            $montoCalculado = $montoCalculado - $puntosAusar;

            if ($isPagoEnAbonos == "true") {
                $montoCalculado =   $montoCalculado/2;
            }

            if ($montoCalculado !=  $params["totalcompra"]) {
                throw new Exception("El monto es erroneo");
            }

            


            //verificamos la cantidad de boletos a comprar:
            //El objeto params, ya debe traer del front el tipo de pago $params["metodopago"],
           
            $params['numboletoscompra'] = 0;
            foreach ($params['boletos'] as $key => $value) {
                $params['numboletoscompra'] += $value["cantidad"];
            }

           

            //Despues Generamos orden de compra sin el id de transacción, con estatus 2, creado (pendiente)
            //El orden de id son:
            // 1 Pagado
            // 2 Creado/pendiente
            //-3
            // -1 Otro/Vencido
            // -2 invalido cuando no paso el pago
            $params["estatus"] = 2;
            if ($isPagoEnAbonos == "true") {
                $params["estatus"] = 10;
            }

            //Si es en abonos registramos en la orden el pago completo
            if ($isPagoEnAbonos == "true") {
                $params["totalcompra"] =   $montoCalculado * 2;
            }

            $idOrdenCompra = $this->paymentDao->loadOrdenCompra($params);

            if (!$idOrdenCompra) {
                throw new Exception("No se logro crear la orden de compra");
            }

            $descuentoId = 0;
            if ($cupon["object"] && $cupon["object"] ["iddescuentoevento"]) {
                $descuentoId =$cupon["object"] ["iddescuentoevento"];
                $this->paymentDao->saveOrdenCompraDescuento($descuentoId, $idOrdenCompra);
            }

         

            //creamos los boletos (kardexBoletos)
            $boletosCreados = array();
            foreach ($params['boletos'] as $key => $value) {
                $boletosCreados = array_merge($boletosCreados, $this->loadKardexBoletos($params["evento"], $value["tipoboleto"], $value["cantidad"], $nombreCliente, $params["cliente"]["correo"], $params["cliente"]["celular"], null, null, $params["estatus"]));
            }

            if (count($boletosCreados) <= 0) {
                throw new Exception('No se lograron crear los boletos');
            }


            //creamos el detalleOrdenCompra (dupla de kardex con ordencompra)
            $paramsDor["idordercompra"] = $idOrdenCompra;
            foreach ($boletosCreados as $key => $idKardexBoletos) {
                $paramsDor["idkardexboletos"] = $idKardexBoletos;

                $adOrdenCompra = $this->paymentDao->loadDetalleOrdenCompra($paramsDor);

                if (!$adOrdenCompra) {
                    throw new Exception('No se lograron ligar los boletos');
                }
            }

  
            //creamos el objeto cargo hacía OpenPay
            $cargo->setCurrency =  "MXN";

            //Objeto customer para el cargo a OpenPay:
            $customer = array(
                "name" => $params["cliente"]["nombre"],
                "last_name" => $params["cliente"]["apellidos"],
                "phone_number" => $params["cliente"]["celular"],
                "email" => $params["cliente"]["correo"]
            );
                        
            
            
            
           
           

            $urlRedirect = "";
            $monto =  $params["totalcompra"];
           
            if ($isPagoEnAbonos == "true") {
                $urlRedirect = URL_BASE_PAGOS.'event/success-abono.php';
                $monto = $monto/2;
            } else {
                $urlRedirect =  URL_BASE_PAGOS.'event/success.php';
            }
            $chargeRequest=null;

           
           
            $monto = round($monto, 2);
            if ($params["metodopago"] == "store") {
                $chargeRequest = array(
                    "method" => $params["metodopago"],
                    'amount' => $monto,
                    'description' => $evento['nombreevento']." | ".$idOrdenCompra,
                    'customer' => $customer,
                    'send_email' => false,
                    'confirm' => false,
                    'due_date' => date(DateTime::ISO8601, strtotime(' +1 day')),
                    'redirect_url' => $urlRedirect )
                ;
            } else {
                $chargeRequest = array(
                    "method" => $params["metodopago"],
                    'amount' => $monto,
                    'description' => $evento['nombreevento']." | ".$idOrdenCompra,
                    'customer' => $customer,
                    'send_email' => false,
                    'confirm' => false,
                    'due_date' => date(DateTime::ISO8601, strtotime('  +20 minute')),
                    'use_3d_secure' => 'true',
                    'redirect_url' => $urlRedirect );
            }
       
            $payResponse = $this->openPayDao->makeAPay($chargeRequest);
           

            if (!isset($payResponse) || $payResponse->id==null || $payResponse->id == "") {
                throw new Exception("No fue posible crear el proceso de pago en OpenPay");
            }
            

            $params["idreferenciapago"] = $payResponse->id;
            $params["idordercompra"] = $idOrdenCompra;

            //hacemos que se guarde todo OK hasta ahora
            // $this->paymentDao->commitTransaction();

            // $this->paymentDao->beginTransaction();
            //actualizamos la referencia de pago de la orden de compra
            $this->paymentDao->updateIdReferenciaOrdenCompra($params);

            

            $isAbonoRegistryCompleted = false;
            if ($isPagoEnAbonos == "true") {
                $isAbonoRegistryCompleted = $this->paymentDao->agregarAbonoPago($idOrdenCompra, $payResponse->id, $monto, 0);
            }

            if ($isPagoEnAbonos == "true" && !$isAbonoRegistryCompleted) {
                throw new Exception("No se logro crear el abono de la compra");
            }

            //validamos si es pago en comercio o en linea
            if ($payResponse->payment_method->type =="store") {
                $evento = "evento prueba";
                $monto =  $params["totalcompra"];
                $orden =  $paramsDor["idordercompra"];
                $code = $payResponse->payment_method->barcode_url;
                $refcode= $payResponse->payment_method->reference;
                
                $paymentPath ="";
                if(PAYMENT_MODE == "dev"){
                    $paymentPath = DASHBOARD_PATH_DEV;
                } else {
                    $paymentPath = DASHBOARD_PATH_PRODUCTION;
                }

                $urlPDF = $paymentPath.$config["id"]."/".$refcode;
                
                $responseObj =  array(
                    "payI" => $payResponse->id,
                    "method" => $payResponse->payment_method,
                    "ordencompra" => $paramsDor["idordercompra"],
                    "urlPagoEnComercio" =>  $urlPDF
                );
            } else {
                $responseObj =  array(
                    "payI" => $payResponse->id,
                    "method" => $payResponse->payment_method,
                    "ordencompra" => $paramsDor["idordercompra"]
                );
            }

           

            if ($payResponse) {
                $response->setSuccess(true);
                $response->setMessage("Registro guardado correctamente");
                $response->setObject($responseObj);
                $this->paymentDao->commitTransaction();
            } else {
                $response->setSuccess(false);
                $response->setMessage("Ocurrio un error al registrar la solicitud de pago");
                $response->setObject(null);
                $this->paymentDao->rollBackTransaction();
            }
        } catch (Exception $e) {
            $this->paymentDao->rollBackTransaction();
            $response->setSuccess(false);
            $response->setMessage("Ocurrio una excepcion al registrar la orden de compra: " . $e->getMessage());
            $response->setObject(null);
        }

        return $response->expose();
    }

   

    public function agregarAbono($params)
    {
        $response = new WSResponse();


        $idOrdenCompra = $params['idOrdenCompra'];
        $idReferenciaPago = $params["idReferenciaPago"];
        $monto = $params['monto'];

        $isAbonoRegistryCompleted = $this->carteraDao->agregarAbonoPago($idOrdenCompra, $idReferenciaPago, $monto, 0);
        if ($isAbonoRegistryCompleted) {
            $response->setSuccess(true);
            $response->setMessage("Registro guardado correctamente");
            $response->setObject(null);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al registrar la solicitud de pago");
            $response->setObject(null);
        }
        return $response->expose();
    }


    /**
     * se usa para los siguientes abonos
     */
    public function pagarAbono($params)
    {
        $response = new WSResponse();
        try {
            $this->carteraDao->beginTransaction();


            $urlRedirect =  URL_BASE_PAGOS."event/success-abono.php";
            $monto =  $params["totalcompra"];
            $idOrdenCompra= $params["idOrdenCompra"];

            $customer = array(
                "name" => $params["cliente"]["nombre"],
                "last_name" => $params["cliente"]["apellidos"],
                "phone_number" => $params["cliente"]["celular"],
                "email" => $params["cliente"]["correo"]
            );
                        
            
            $chargeRequest = array(
                "method" => "card",
                'amount' => $monto,
                'description' => "Abono para la compra| ".$idOrdenCompra,
                'customer' => $customer,
                'send_email' => false,
                'confirm' => false,
                'redirect_url' => $urlRedirect );

            $payResponse = $this->openPayDao->makeAPay($chargeRequest);

        
            
            if (!isset($payResponse) || $payResponse->id==null || $payResponse->id == "") {
                throw new Exception("No fue posible crear el proceso de pago en OpenPay");
            }

            $isAbonoRegistryCompleted = false;
            $isAbonoRegistryCompleted = $this->carteraDao->agregarAbonoPago($idOrdenCompra, $payResponse->id, $monto, 0);
        
            if (!$isAbonoRegistryCompleted) {
                throw new Exception("No se logro crear el abono de la compra");
            }

           

            $responseObj =  array(
                "payI" => $payResponse->id,
                "method" => $payResponse->payment_method,
                "ordencompra" => $idOrdenCompra
            );

            
            if ($isAbonoRegistryCompleted) {
                $this->carteraDao->commitTransaction();
                $response->setSuccess(true);
                $response->setMessage("Registro guardado correctamente");
                $response->setObject($responseObj);
                //////////////////////////////////
                $this->paymentDao->beginTransaction();
                try {
                    $params['idreferenciapago'] =  $payResponse->id;
                    $params['idordercompra'] = $idOrdenCompra;
                    $this->paymentDao->updateIdReferenciaOrdenCompra($params);
                    $this->paymentDao->commitTransaction();
                } catch (Exception $e) {
                    $this->paymentDao->rollBackTransaction();
                    throw new Exception("No se logro cambiar el idreferenciapago en la orden de compra");
                }
            } else {
                $response->setSuccess(false);
                $response->setMessage("Ocurrio un error al registrar la solicitud de pago");
                $response->setObject(null);
                $this->carteraDao->rollBackTransaction();
            }
        } catch (Exception $e) {
            $this->carteraDao->rollBackTransaction();
            $this->paymentDao->rollBackTransaction();

            $response->setSuccess(false);
            $response->setMessage("Ocurrio una excepcion al registrar la orden de compra 1: " . $e->getMessage());
            $response->setObject(null);
        }
        return $response->expose();
    }

    public function confirmarPagoAbono($params)
    {
        try {
            $this->carteraDao->beginTransaction();
            $idReferenciaPago = $params["idReferenciaPago"];
            $cargo = $this->openPayDao->getPayStatus($idReferenciaPago);
         
            $isCompleted = false;
            if ($cargo->status == "completed") {
                $isCompleted = $this->carteraDao->actualizarEstatusAbonoPago($idReferenciaPago, 1);
                $idOrdenCompra = $this->paymentDao->getIdOdenCompraFromIdReferenciaPago($idReferenciaPago)[0]["idordercompra"];
                $isComplete = $this->validarMontoTotalDeAbonos($idOrdenCompra);
                if ($isComplete) {
                    $this->enviarCorreoBoletos($idReferenciaPago);
                }
            }

            //aggregar codigo para validar si ya esta completo el pago
            $this->carteraDao->commitTransaction();

            return $isCompleted;
        } catch (Exception $e) {
            $this->carteraDao->rollBackTransaction();
            return false;
        }
    }

    public function validarMontoTotalDeAbonos($idOrdenCompra)
    {
        $abonos = $this->carteraDao->getAbonosPago($idOrdenCompra);
        $ordenCompra = $this->paymentDao->getOrdenCompra($idOrdenCompra);

        $totalApagar = $ordenCompra[0]["totalcompra"];
        $totalPagado = 0;
        foreach ($abonos as $key => $abono) {
            $totalPagado+= $abono["montopagado"];
        }

        if ($totalApagar <= $totalPagado) {
            $this->paymentDao->updateEstatusOrdenCompraPagadaFromAbonos($idOrdenCompra);
            $this->paymentDao->setBoletosPagadosByIdOrdenCompra($idOrdenCompra);
            return true;
        }
        return false;
    }

    public function agregarPuntosCartera($params)
    {
        $response = new WSResponse();

        $cargo = new CargoDTO();

        $idUsuario = $params['usuario'];
        $monto = $params['monto'];

        //$this->paymentDao->beginTransaction();

        $cargo->setCurrency =  "MXN";
        
        $customer = array(
            "name" => $params["cliente"]["nombre"],
            "last_name" => $params["cliente"]["apellidos"],
            "phone_number" => $params["cliente"]["celular"],
            "email" => $params["cliente"]["correo"]
        );
                     
       
        $chargeRequest = array(
           "method" => "card",
           'amount' => $monto,
           'description' => 'punstos para usuario: '.$params["cliente"]["correo"],
           'customer' => $customer,
           'send_email' => false,
           'confirm' => false,
           'due_date' => date(DateTime::ISO8601, strtotime('  +10 minute')),
           'redirect_url' =>  URL_BASE_PAGOS.'event/success-cartera.php' );

        $payResponse = $this->openPayDao->makeAPay($chargeRequest);

       
       
        if (!isset($payResponse) || $payResponse->id==null || $payResponse->id == "") {
            throw new Exception("No fue posible crear el proceso de pago en OpenPay");
        }

        $idCartera = null;
        $cartera= $this->carteraDao->getCartera($idUsuario);

        
        //traemos el último registro (debe ser unico ) o creamos el primero (se crea con 0)
        if (count($cartera) >0) {
            $idCartera = $cartera[0]["idcartera"] ;
        } else {
            //beggin transaction
            $this->carteraDao->beginTransaction();

            $idCartera = $this->carteraDao->createCartera(0, $idUsuario);
            //end transaction
            $this->carteraDao->commitTransaction();
        }

       


        //creamos un nuevo abono apuntando al último registro de cartera
        //si en la confirmaión, pasa el pago, se le sumará lo de este abono
        $this->carteraDao->setAbonoCartera($payResponse->id, $idUsuario, $monto, $idCartera);

        $responseObj =  array(
            "payI" => $payResponse->id,
            "method" => $payResponse->payment_method
        );
        if ($payResponse) {
            $response->setSuccess(true);
            $response->setMessage("Puntos guardados correctamente");
            $response->setObject($responseObj);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio un error al registrar la solicitud de pago");
            $response->setObject(null);
        }

        return $response->expose();
    }

    //por el momento el metodo es de uso interno, si se requiere exponer hacer uno que incorpore el wsresponse
    public function quitarPuntosCartera($params)
    {
        $idUsuario = $params['usuario'];
        $monto = $params['monto'];

        
        $idCartera = null;
        $cartera= $this->carteraDao->getCartera($idUsuario);

        
        //traemos cartera y si no existe adios
        if (count($cartera) >0) {
            $idCartera = $cartera[0]["idcartera"] ;
        } else {
            throw new Exception("No tiene cartera!");
        }

        $nuevoMonto = $cartera[0]["puntosdisponibles"] - $monto;
        $this->carteraDao->sumarPuntosCartera(-$monto, $idCartera);
        //creamos un nuevo abono apuntando al  registro de cartera
        //si en la confirmaión, pasa el pago, se le sumará lo de este abono
       // $this->carteraDao->setAbonoCartera($payResponse->id, $idUsuario, $monto, $idCartera);
    }


    public function confirmarAbonoCartera($params)
    {
        $response = new WSResponse();

        $idReferenciaPago = $params["idReferenciaPago"];

        //Obtenemos el abono que se hizo con esa referencia
        $abono =  $this->carteraDao->getAbonoByIdReferencia($idReferenciaPago);
        $isCompleted = false;
        $monto = 0;

        //falta confirmacion de pago
        $cargo = $this->openPayDao->getPayStatus($idReferenciaPago);
        $isCompleted = false;
       



        if (isset($abono) && $cargo->status == "completed") {
            $monto = $abono[0]['montoingresado'];
        }

        $isCompleted=  $this->carteraDao->sumarPuntosCartera($monto, $abono[0]["idcartera"]);


        if ($isCompleted && $cargo->status == "completed") {
            $response->setSuccess(true);
            $response->setMessage("Abono confirmado");
            $response->setObject(null);
        } else {
            $response->setSuccess(false);
            $response->setMessage("Abono no confirmado");
            $response->setObject(null);
        }

        return $response->expose();
    }


    public function confirmPayment($idReferenciaPago)
    {
        $response = new WSResponse();
        try {
            $cargo = $this->openPayDao->getPayStatus($idReferenciaPago);
            $mensaje= "";
            $params['idReferenciaPago'] = $idReferenciaPago;
            $params["estatus"] = 2;
            $mensaje= "pendiente";
            $params["cargo"] = $cargo;
            if (is_object($cargo)) {
                if ($cargo->status == "completed") {
                    $params['estatus'] = 1;
                    $this->paymentDao->updateEstatusOrdenCompra($params);
                    $isUpddated = $this->paymentDao->setBoletosPagadosByIdReferencia($idReferenciaPago[0]['idreferenciapago']);
                    if ($isUpddated) {
                        $this->enviarCorreoBoletos($idReferenciaPago[0]['idreferenciapago']);
                    }
                } elseif ($cargo->method == "store") {
                    $limite =  new DateTime($cargo->due_date);
                    $today = new DateTime('now');
                   
                    if ($today > $limite) {
                        $params['estatus'] = -1;
                        $this->paymentDao->updateEstatusOrdenCompra($params);
                        $this->paymentDao->setBoletosVencidosByIdReferencia($idReferenciaPago[0]['idreferenciapago']);
                    }
                } elseif ($cargo->method == "card") {
                    //entra aqui solo si no esta completo y no es de tienda
                    //aqui borraremos los datos de los boletos

                    $limite =  new DateTime($cargo->due_date);
                    $today = new DateTime('now');

                    if ($today > $limite) {
                        $params['estatus'] = -1;
                        $mensaje= "vencido";
                        $this->paymentDao->updateEstatusOrdenCompra($params);
                        $this->paymentDao->setBoletosVencidosByIdReferencia($idReferenciaPago[0]['idreferenciapago']);
                    }
                }
            } else {
                //si no encuentra el cargo en OpenPay:
                //evaluaremos las fechas de la orden de compra vs 2 horas

                $ordenCompra = $this->paymentDao->getOrdenCompra($idReferenciaPago[0]['idreferenciapago'])[0];
                $limite =  new DateTime($ordenCompra['fechaalta']);
                $limite->modify('+ 10 minute');

       
                $today = new DateTime('now');
                if ($today > $limite) {
                    $params['estatus'] = -1;
                    $mensaje= "vencido";
                    $this->paymentDao->updateEstatusOrdenCompra($params);
                    $this->paymentDao->setBoletosVencidosByIdReferencia($idReferenciaPago[0]['idreferenciapago']);
                }
            }
            
            $response->setSuccess(true);
            $response->setMessage($mensaje);
            $response->setObject($params);
        } catch (Exception $e) {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio una excepcion al registrar la orden de compra 2: " . $e->getMessage());
            $response->setObject(null);
        }
        return $response->expose();
    }

    public function confirmPaymentWithIdOrdenCompra($idOrdenCompra)
    {
        $response = new WSResponse();
        try {
            $idReferenciaPago =$this->paymentDao->getIdReferenciaOfOrdenCompra($idOrdenCompra);
             
           
            $cargo = $this->openPayDao->getPayStatus($idReferenciaPago[0]['idreferenciapago']);
            
            
            $params['idReferenciaPago'] = $idReferenciaPago[0]['idreferenciapago'];

            $params["estatus"] = 2;

            if ($cargo->status == "completed") {
                $params['estatus'] = 1;
                $this->paymentDao->updateEstatusOrdenCompra($params);
                $this->paymentDao->setBoletosPagadosByIdReferencia($idReferenciaPago[0]['idreferenciapago']);
            } elseif ($cargo->method == "store") {
                $limite =  new DateTime($cargo->due_date);
                $today = new DateTime('now');
                if ($today > $limite) {
                    $params['estatus'] = -1;
                    $this->paymentDao->updateEstatusOrdenCompra($params);
                    //$this->paymentDao->setBoletosPagadosByIdReferencia($idReferenciaPago[0]['idreferenciapago']);
                }
            } elseif ($cargo->method == "card") {
                //entra aqui solo si no esta completo y no es de tienda
                //aqui borraremos los datos de los boletos
                $fechaCreacion =  new DateTime($cargo->creation_date);
                $limite =  strtotime($fechaCreacion."+ 1 days");
                $today = new DateTime('now');
               
                if ($today > $limite) {
                    $params['estatus'] = -1;
                    $mensaje= "vencido";
                    $this->paymentDao->updateEstatusOrdenCompra($params);
                    //$this->paymentDao->setBoletosPagadosByIdReferencia($idReferenciaPago[0]['idreferenciapago']);
                }
            }


            $response->setSuccess(true);
            $response->setMessage("pago confirmado");
            $response->setObject($params);
        } catch (Exception $e) {
            $response->setSuccess(false);
            $response->setMessage("Ocurrio una excepcion al registrar la orden de compra 3: " . $e->getMessage());
            $response->setObject(null);
        }
        return $response->expose();
    }

    public function actualizarBoletosPendientesoCancelar()
    {
        $exitosos =[];
        try {
            $ordenesCompra = $this->boletosDao->getOrdenesCompraDeBoletosPendientes();
            foreach ($ordenesCompra as $key => $idOrdenCompra) {
                $this->confirmPaymentInterno($idOrdenCompra['idordercompra']);
                array_push($exitosos, $idOrdenCompra['idordercompra']);
            }
        } catch (Exception $e) {
            throw new Exception("Ocurrio un error en actualizar o cancelar");
        }
        return $exitosos;
    }

    //Este servicio se usara para el "cron"
    //eventualmente solo se utilizara este
    public function confirmPaymentInterno($idOrdenCompra)
    {
        try {
            $this->paymentDao->beginTransaction();

            $idReferenciaPago =$this->paymentDao->getIdReferenciaOfOrdenCompra($idOrdenCompra);
            $cargo = $this->openPayDao->getPayStatus($idReferenciaPago[0]['idreferenciapago']);
            $params['idReferenciaPago'] = $idReferenciaPago[0]['idreferenciapago'];
            $params["estatus"] = 2;
            
            if (is_object($cargo)) {
                if ($cargo->status == "completed") {
                    $params['estatus'] = 1;
                    $this->paymentDao->updateEstatusOrdenCompra($params);
                    $isUpddated = $this->paymentDao->setBoletosPagadosByIdReferencia($idReferenciaPago[0]['idreferenciapago']);
                    if ($isUpddated) {
                        $this->enviarCorreoBoletos($idReferenciaPago[0]['idreferenciapago']);
                    }
                } elseif ($cargo->method == "store") {
                    $limite =  new DateTime($cargo->due_date);
                    $today = new DateTime('now');
                   
                    if ($today > $limite) {
                        $params['estatus'] = -1;
                        $this->paymentDao->updateEstatusOrdenCompra($params);
                        $this->paymentDao->setBoletosVencidosByIdReferencia($idReferenciaPago[0]['idreferenciapago']);
                    }
                } elseif ($cargo->method == "card") {
                    //entra aqui solo si no esta completo y no es de tienda
                    //aqui borraremos los datos de los boletos

                    $limite =  new DateTime($cargo->due_date);
                    $today = new DateTime('now');

                    if ($today > $limite) {
                        $params['estatus'] = -1;
                        $mensaje= "vencido";
                        $this->paymentDao->updateEstatusOrdenCompra($params);
                        $this->paymentDao->setBoletosVencidosByIdReferencia($idReferenciaPago[0]['idreferenciapago']);
                    }
                }
            } else {
                //si no encuentra el cargo en OpenPay:
                //evaluaremos las fechas de la orden de compra vs 2 horas

                $ordenCompra = $this->paymentDao->getOrdenCompra($idOrdenCompra)[0];
                $limite =  new DateTime($ordenCompra['fechaalta']);
                $limite->modify('+ 10 minute');

       
                $today = new DateTime('now');
                if ($today > $limite) {
                    $params['estatus'] = -1;
                    $mensaje= "vencido";
                    $this->paymentDao->updateEstatusOrdenCompra($params);
                    $this->paymentDao->setBoletosVencidosByIdReferencia($idReferenciaPago[0]['idreferenciapago']);
                }
            }

           

            $this->paymentDao->commitTransaction();
        } catch (Exception $e) {
            $this->paymentDao->rollBackTransaction();
            throw new Exception("Ocurrio un error al moemnto de actualizar");
        }
    }

    private function enviarCorreoBoletos($idTransaction)
    {
        $key = "compra-0-{$idTransaction}";
        $key = $this->util->encrypt($key);
        $redirect = URL_BASE."document/boletos/?key={$key}";

        $contactos = $this->boletosService->getContactInfoToNotifyOnFullPayment($idTransaction);
        foreach ($contactos as $key => $value) {
            if (isset($value["correopersona"])) {
                $params["subject"] = "Descarga tu boleto";
                $params["from"] = "servicio@ticketapp.com";
                $params["fromName"] = "Ticket App";
                $params["toArray"] = $value["correopersona"];                

                
                $mensajes = $this->eventDao->mensajesEventoConfirmacion($value["idevento"]);
                $params["body"] = "Por favor descarga tu boleto, gracias por tu preferencia <a href='{$redirect}' _target='blank'>Descargar</a></b>";
                if( $mensajes && count( $mensajes ) > 0 ){
                    $params["body"] = $mensajes[0]['correo']." <a href='{$redirect}' _target='blank'>Descargar</a></b>";

                }
                return $this->util->sendMail($params);

            }
            if (isset($value["telefonopersona"])) {
                $params['cellPhone'] = $value["telefonopersona"];
                $params["message"] = "Por favor descarga tu boleto, gracias por tu preferencia $redirect";
                return $this->util->sendWhatsapp($params);
            }
        }
    }
    private function enviarCorreoBoletosPDF($idTransaction, $urlPDF)
    {
        $key = "compra-0-{$idTransaction}";
        $key = $this->util->encrypt($key);

        $contactos = $this->boletosService->getContactInfoToNotifyOnFullPayment($idTransaction);
        foreach ($contactos as $key => $value) {
            if (isset($value["correopersona"])) {
                $params["subject"] = "Descarga tu boleto";
                $params["from"] = "servicio@ticketapp.com";
                $params["fromName"] = "Ticket App";
                $params["toArray"] = $value["correopersona"];                

                
                $mensajes = $this->eventDao->mensajesEventoConfirmacion($value["idevento"]);
                $params["body"] = "Por favor descarga el formato de pago en tiendas de conveniencia, gracias por tu preferencia: <a href='{$urlPDF}' _target='blank'>Descargar PDF</a></b>";
                
                return $this->util->sendMail($params);

            }
            if (isset($value["telefonopersona"])) {
                $params['cellPhone'] = $value["telefonopersona"];
                $params["message"] = "Por favor descarga el formato de pago en tiendas de conveniencia, gracias por tu preferencia: <a href='{$urlPDF}' _target='blank'>Descargar PDF</a></b>";
                return $this->util->sendWhatsapp($params);
            }
        }
    }

    private function toObject($arr, $objectName)
    {
        if (is_array($arr)) {
            // Return object
            return (object) array_map($objectName, $arr);
        }
        return false;
    }
}
