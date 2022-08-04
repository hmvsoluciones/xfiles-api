<?php

use GuzzleHttp\Psr7\Response;

class MovilServiceImpl implements MovilService {

    private $movilDao;

    function __construct() {
        $this->movilDao = new MovilDaoImpl();
    }

    public function getEventsAppMovil($idUser, $role) {
        $response = new WSResponse();
        if($role == "PUNTO DE VENTA"){
            $data =    $this->movilDao->getEventsForPuntoVentaUser($idUser);
        } else if($role == "APPMOVIL"){
            $data =    $this->movilDao->getEventsForAppMovilUser($idUser);
        } else {
            $data = null;
        }   
        
        if($data){
            $response->setSuccess(true);
            $response->setMessage("Registros obtenidos correctamente");
            $response->setObject($data);
        } else {
            $response->setSuccess(false);
            $response->setMessage("No fue posible obtener los registros");
            $response->setObject(null);
        }

        return $response->expose();
    }

    public function activarTicket($idUser, $idEvent, $folioBoleto) {
        $response = new WSResponse();
        $boletoValido = $this->movilDao->esBoletoFisicoValido($idEvent, $idUser, $folioBoleto);

        if($boletoValido){
            if($boletoValido[0]["estatus"] == 1){
                $response->setSuccess(false);
                $response->setMessage("El boleto ya se encuentra activado");
                $response->setObject(null);
            } if($boletoValido[0]["estatus"] == 3){
                if($this->movilDao->activarBoletoFisico($boletoValido[0]["folioboletos"])){
                    $response->setSuccess(true);
                    $response->setMessage("Boleto activado");
                    $response->setObject(null);
                } else {
                    $response->setSuccess(false);
                    $response->setMessage("No fue posible activar el boleto");
                    $response->setObject(null);
                }
            } else {
                $response->setSuccess(false);
                $response->setMessage("No es posible activar el boleto favor de contactar al administrador");
                $response->setObject(null);
            }
        } else {
            $response->setSuccess(false);
            $response->setMessage("El boleto no corresponde al evento, favor de verificar");
            $response->setObject(null);
        }
        return $response->expose();
    }

    public function paseEventoTicket($idUser, $idEvent, $folioBoleto) {
        $response = new WSResponse();
        $boletoValido = $this->movilDao->esBoletoPaseEventoValido($idEvent, $idUser, $folioBoleto);

        if($boletoValido){
            if($boletoValido[0]["ingreso"] == 1){
                
                $response->setSuccess(false);
                $response->setMessage("El boleto ya ingreso al evento");
                $response->setObject(null);
                
            } else if($boletoValido[0]["ingreso"] == 0){


                if($this->movilDao->esCortesiaByFolioBoleto($boletoValido[0]["folioboletos"]) || $this->movilDao->esListaInvitadosByFolioBoleto($$boletoValido[0]["folioboletos"])){
                    
                    if($this->movilDao->esValidoPagoSinMedioBoleto($boletoValido[0]["fechainicio"], $boletoValido[0]["horafinboletogratis"])){                      

                        if($this->movilDao->paseEvento($boletoValido[0]["folioboletos"])){
                            $response->setSuccess(true);
                            $response->setMessage("Pase autorizado");
                            $response->setObject(null);
                        } else {
                            $response->setSuccess(false);
                            $response->setMessage("No fue posible registrar el pase, favor de intentar nuevamente");
                            $response->setObject(null);
                        }
                    } else {
                        if($this->movilDao->paseEventoConMedioBoleto($boletoValido[0]["folioboletos"])){
                            $response->setSuccess(true);
                            $response->setMessage("Realizar pago de medio boleto en taquilla, vencio la hora de pago de medio boleto");
                            $response->setObject(null);
                        } else {
                            $response->setSuccess(false);
                            $response->setMessage("No fue posible registrar el pase, favor de intentar nuevamente");
                            $response->setObject(null);
                        }
                    }
                } else {
                
                    if($this->movilDao->paseEvento($boletoValido[0]["folioboletos"])){
                        $response->setSuccess(true);
                        $response->setMessage("Pase autorizado");
                        $response->setObject(null);
                    } else {
                        $response->setSuccess(false);
                        $response->setMessage("No fue posible registrar el pase, favor de intentar nuevamente");
                        $response->setObject(null);
                    }
                }
               
            } else {
                $response->setSuccess(false);
                $response->setMessage("No fue posible registrar el ingreso, favor de contactar al administrador");
                $response->setObject(null);
            }
        } else {
            $response->setSuccess(false);
            $response->setMessage("El boleto no esta autorizado para el  evento, favor de verificar");
            $response->setObject(null);
        }
        return $response->expose();
    }
    public function activarTicketGlobal($idUser, $folioBoleto) {
        $response = new WSResponse();
        $boletoValido = $this->movilDao->getKardexByFolioBoleto($folioBoleto);

        if($boletoValido){
            if(intval($boletoValido[0]["estatus"]) == 1){
                $response->setSuccess(false);
                $response->setMessage("El boleto ya se encuentra activado");
                $response->setObject(null);
            } else if($boletoValido[0]["estatus"] == 3){
                if($this->movilDao->activarBoletoFisicoGlobal($boletoValido[0]["folioboletos"], $idUser)){
                    $response->setSuccess(true);
                    $response->setMessage("Boleto activado");
                    $response->setObject(null);
                } else {
                    $response->setSuccess(false);
                    $response->setMessage("No fue posible activar el boleto");
                    $response->setObject(null);
                }
            } else {
                $response->setSuccess(false);
                $response->setMessage("Error, el boleto no es un boleto fisico");
                $response->setObject(null);
            }
        } else {
            $response->setSuccess(false);
            $response->setMessage("Error, el boleto no fue encontrado");
            $response->setObject(null);
        }
        return $response->expose();
    }

    public function paseEventoTicketGlobal($idUser, $folioBoleto) {
        $response = new WSResponse();
        $boletoValido = $this->movilDao->getKardexByFolioBoleto($folioBoleto);

        if($boletoValido){
            // 1 ingreso normal, 2= ingreso con medio boleto
            if($boletoValido[0]["ingreso"] == 1 || $boletoValido[0]["ingreso"] == 2){
                
                $response->setSuccess(false);
                $response->setMessage("Error, el boleto ya ingreso al evento");
                $response->setObject(null);
                
            } else if($boletoValido[0]["ingreso"] == 0 && $boletoValido[0]["estatus"] != 3 && $boletoValido[0]["estatus"] != -3){


                if($this->movilDao->esCortesiaByFolioBoleto($boletoValido[0]["folioboletos"]) || $this->movilDao->esListaInvitadosByFolioBoleto($$boletoValido[0]["folioboletos"])){
                   
                    $eventoFechaMedioBoleto = $this->movilDao->getFechaHoraFinListaCortesia($boletoValido[0]["folioboletos"]);

                    //FEcha inicio del evento y hora del evento boleto gratis
                    if($this->movilDao->esValidoPagoSinMedioBoleto($eventoFechaMedioBoleto[0]["fechainicio"], $eventoFechaMedioBoleto[0]["horafinboletogratis"])){                      

                        if($this->movilDao->paseEventoGlobal($boletoValido[0]["folioboletos"], $idUser)){
                            $response->setSuccess(true);
                            $response->setMessage("Pase autorizado(lista, cortesia)");
                            $response->setObject(null);
                        } else {
                            $response->setSuccess(false);
                            $response->setMessage("No fue posible registrar el pase, favor de intentar nuevamente");
                            $response->setObject(null);
                        }
                    } else {
                        if($this->movilDao->paseEventoConMedioBoletoGlobal($boletoValido[0]["folioboletos"], $idUser)){
                            $response->setSuccess(true);
                            $response->setMessage("Realizar pago de medio boleto en taquilla, vencio la hora de pago de medio boleto");
                            $response->setObject(null);
                        } else {
                            $response->setSuccess(false);
                            $response->setMessage("No fue posible registrar el pase con medio boleto, favor de intentar nuevamente");
                            $response->setObject(null);
                        }
                    }
                } else {
                
                    if($this->movilDao->paseEventoGlobal($boletoValido[0]["folioboletos"], $idUser)){
                        $response->setSuccess(true);
                        $response->setMessage("Pase autorizado");
                        $response->setObject(null);
                    } else {
                        $response->setSuccess(false);
                        $response->setMessage("No fue posible registrar el pase, favor de intentar nuevamente");
                        $response->setObject(null);
                    }
                }
               
            } else {
                $response->setSuccess(false);
                $response->setMessage("Boleto no valido, favor de contactar al administrador(Verifique que sea un boleto activo)");
                $response->setObject(null);
            }
        } else {
            $response->setSuccess(false);
            $response->setMessage("Error, boleto no encontrado");
            $response->setObject(null);
        }
        return $response->expose();
    }

}