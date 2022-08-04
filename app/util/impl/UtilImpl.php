<?php
//require_once '../ticketapp-systems/libs/PHPMailer/src/PHPMailer.php';
use Twilio\Rest\Client;
/*use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;*/

class UtilImpl implements Util
{

    public function decrypt($textToDecrypt)
    {

        $config_sec = parse_ini_file(__DIR__ . '../../../config/config_security.ini');

        $password = $config_sec['encrypKey'];

        $textToDecrypt = base64_decode($textToDecrypt);
        $method = "AES-256-CBC";
        $iv = substr($textToDecrypt, 0, 16);
        $hash = substr($textToDecrypt, 16, 32);
        $ciphertext = substr($textToDecrypt, 48);
        $key = hash('sha256', $password, true);

        if (hash_hmac('sha256', $ciphertext, $key, true) !== $hash)
            return null;

        return openssl_decrypt($ciphertext, $method, $key, OPENSSL_RAW_DATA, $iv);
    }

    public function encrypt($textToEncrypt)
    {

        $config_sec = parse_ini_file(__DIR__ . '../../../config/config_security.ini');

        $password = $config_sec['encrypKey'];

        $method = "AES-256-CBC";
        $key = hash('sha256', $password, true);
        //$iv = openssl_random_pseudo_bytes(16);
        $iv = $config_sec['encrypIV'];

        $ciphertext = openssl_encrypt($textToEncrypt, $method, $key, OPENSSL_RAW_DATA, $iv);
        $hash = hash_hmac('sha256', $ciphertext, $key, true);

        return base64_encode($iv . $hash . $ciphertext);
    }

    public function sendMail($params)
    {
        try {
            // Create the Transport
            $config_mail = parse_ini_file(__DIR__ . '../../../config/config_mail.ini');
            $sent = 0;
            if($config_mail["mode"] == "production"){
                $transport = new Swift_SendmailTransport($config_mail["sendMailParam"]);
                // Create the Mailer using your created Transport
                $mailer = new Swift_Mailer($transport);


                // Create a message
                $message = (new Swift_Message($params["subject"]))
                    ->setFrom([$params["from"] => $params["fromName"]])
                    ->setTo($params["toArray"])
                    ->setBody($this->mailTemplate($params["subject"], $params["body"]), "text/html");
                // Send the message
                $sent = $mailer->send($message);                
            }else if($config_mail["mode"] == "dev"){
                $transport = (new Swift_SmtpTransport(                    
                    $config_mail["host"], 
                    $config_mail["port"], 
                    $config_mail["secure"]))
                ->setUsername($config_mail["mail"])
                ->setPassword($config_mail["password"]);

                // Create the Mailer using your created Transport
                $mailer = new Swift_Mailer($transport);


                // Create a message
                $message = (new Swift_Message($params["subject"]))
                    ->setFrom([$params["from"] => $params["fromName"]])
                    ->setTo($params["toArray"])
                    ->setBody($this->mailTemplate($params["subject"], $params["body"]), "text/html");
                // Send the message
                $sent = $mailer->send($message);
                
            } else if($config_mail["mode"] == "mandrill"){
                $curlOptions = array(
                    CURLOPT_POST => true,
                    /*CURLOPT_POSTFIELDS => "{
                        'key':'{$config_mail["mandrill_key"]}',
                        'message':{
                            'html': '{$params["body"]}',                            
                            'subject':'{$params["subject"]}',
                            'from_email':'{$config_mail["mandrill_mail"]}',
                            'from_name':'{$config_mail["mandrill_from"]}',
                            'to':[{
                                'email':'{$params["toArray"]}'	
                            }],
                            'important': true
                            
                        }
                    }",*/
                    CURLOPT_POSTFIELDS => '{
                        "key":"'.$config_mail["mandrill_key"].'",
                        "message":{
                            "html": "'. $this->mailTemplateMandrill($params["subject"], $params["body"]).'",
                            "subject":"'.$params["subject"].'",
                            "from_email":"'.$config_mail["mandrill_mail"].'",
                            "from_name":"'.$config_mail["mandrill_from"].'",
                            "to":[{
                                "email":"'.$params["toArray"].'"	
                            }],
                            "important": true
                            
                        }
                    }',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_VERBOSE => true
                );
                
                $ch = curl_init('https://mandrillapp.com/api/1.0/messages/send');
                curl_setopt_array($ch, $curlOptions);
                $responseCurl = curl_exec($ch);
                $dboxResponse = json_decode($responseCurl);
                curl_close($ch);
                $sent = 1;
            }
            
               

            

            if($sent == 1){
                return "success";
            } else {
                return "error";
            }

        } catch (Exception $e) {
            return "error: ".$e->getMessage();
        }
    }
    public function mailTemplateMandrill($titulo, $body){
        return "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'> <html xmlns='http://www.w3.org/1999/xhtml' xml:lang='es' lang='es'> <head> <meta http-equiv='Content-Type' content='text/html; charset=utf-8' /> <meta name='viewport' content='width=device-width, initial-scale=1.0'> <meta http-equiv='X-UA-Compatible' content='ie-edge'> </head> <body style='margin: 0 auto;'> <table width='100%' align='center' border='0' cellpadding='0' cellspacing='0' style='width:100%;max-width:600px;'> <!-- header --> <tr> <td align='center' bgcolor='#560357' style='padding-top:40px;padding-bottom:40px;background: -moz-linear-gradient(top, #7E1984 0%, #560357 100%);background: -webkit-linear-gradient(top, #7E1984 0%, #560357 100%);background: linear-gradient(to bottom, #7E1984 0%, #560357 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#7E1984', endColorstr='#560357',GradientType=1 );'> <a href='https://ticketapp.live/' target='_blank' border='0' style='text-decoration:none'> <img src='https://ticketapp.live/assets/images/mail/ticketapp_logo@2x.png' alt='Logo Ticketapp' border='0' width='208' style='display:block;border:0;width: 208px;height:auto;'> </a> </td> </tr> <tr> <td style='padding-top:5px;padding-bottom:5px' bgcolor='#7E1984'></td> </tr> <!-- Main --> <tr> <td> <table width='100%' align='center' border='0' cellpadding='0' cellspacing='0' bgcolor='#560357'> <tr> <td colspan='2' style='padding-top:30px; padding-right: 10px; padding-left: 10px; font-family: Arial, sans-serif; font-size: 25px; color: #ffffff; text-align: left; line-height:26px;'>{$titulo}</td> </tr> <tr> <td width='50%' style='width: 50%;max-width: 50%;padding-top:10px; padding-bottom:20px; padding-left: 10px; padding-right: 10px; font-family: Arial, sans-serif; font-size: 14px; color: #ffffff; text-align: left; line-height:26px;'> {$body} </td> <td width='50%' style='width: 50%;max-width: 50%;'><img src='https://ticketapp.live/assets/images/mail/yey@2x.png' alt='Logo Ticketapp' border='0' width='100%' style='display:block;border:0;width: 100%;height:auto;'></td> </tr> </table> </td> </tr> <!-- Dirección --> <!-- footer --> <tr> <td> <table width='100%' align='center' border='0' cellpadding='0' cellspacing='0' bgcolor='#ffffff' style='padding-top: 20px;font-family: Arial, sans-serif;color:#560357'> <tr> <td style='text-align: center;font-size: 14px;' colspan='3'>Síguenos en nuestras Redes sociales ♥</td> </tr> <tr> <td width='33%' style='width: 33%;'></td> <td width='33%' style='width: 33%;'> <table width='50%' align='center' border='0' cellpadding='0' cellspacing='0' style='padding-top: 20px;'> <tr> <td> <a href='https://www.facebook.com/ticketappmx/' target='_blank' border='0' style='text-decoration:none'> <img src='https://ticketapp.live/assets/images/mail/Facebook@2x.png' alt='Facebook de Ticketapp' border='0' width='32' style='display:block;border:0;width: 32px;height:auto;'> </a> </td> <td> <a href='https://www.instagram.com/ticketappmx/' target='_blank' border='0' style='text-decoration:none'> <img src='https://ticketapp.live/assets/images/mail/Instagram@2x.png' alt='Instagram de Ticketapp' border='0' width='32' style='display:block;border:0;width: 32px;height:auto;'> </a> </td> </tr> </table> </td> <td width='33%' style='width: 33%;'></td> </tr> <tr> <td style='text-align: center;font-size: 14px;padding-top: 20px' colspan='3'> ticketapp.live es una plataforma que acerca a productores de eventos con asistentes por lo que NO es responsable de la producción y logística del evento. <br> Visita: <a target='_blank' href='https://ticketapp.live/document-pdf/AvisoDePrivacidadTicketApp.live.pdf'>Aviso de privacidad </a> y <a target='_blank' href='https://ticketapp.live/document-pdf/TerminosyCondicionesTicketApp.live.pdf'>Términos y condiciones</a> </td> </tr> </table> </td> </tr> </table> </body> </html>";
    }
    public function mailTemplate($titulo, $body)
    {
        /*$html = "<!DOCTYPE html>"
            . "<html lang='es' xmlns='http://www.w3.org/1999/xhtml' xmlns:o='urn:schemas-microsoft-com:office:office'>"
            . "<head>"
            . "	<meta charset='UTF-8'>"
            . "	<meta name='viewport' content='width=device-width,initial-scale=1'>"
            . "	<meta name='x-apple-disable-message-reformatting'>"
            . "	<title></title>	"
            . "	<style>"
            . "		table, td, div, h1, p {font-family: Arial, sans-serif;}"
            . "	</style>"
            . "</head>"
            . "<body style='margin:0;padding:0;'>"
            . "	<table role='presentation' style='width:100%;border-collapse:collapse;border:0;border-spacing:0;background:#ffffff;'>"
            . "		<tr>"
            . "			<td align='center' style='padding:0;'>"
            . "				<table role='presentation' style='width:602px;border-collapse:collapse;border:1px solid #cccccc;border-spacing:0;text-align:left;'>"
            . "					<tr>"
            . "						<td>							"
            . "							<img src='https://ticketapp-systems.herokuapp.com/admin/assets/images/ticketapp-correo.png' alt='' style=' width:600px; height:auto;display:block;' />                            "
            . "						</td>"
            . "					</tr>"
            . "					<tr>"
            . "						<td style='padding:36px 30px 42px 30px;'>"
            . "							<table role='presentation' style='width:100%;border-collapse:collapse;border:0;border-spacing:0;'>"
            . "								<tr>"
            . "									<td style='padding:0 0 36px 0;color:#153643;'>"
            . "										<h1 style='font-size:24px;margin:0 0 20px 0;font-family:Arial,sans-serif;'>{$titulo}</h1>"
            . "										<p style='margin:0 0 12px 0;font-size:16px;line-height:24px;font-family:Arial,sans-serif;'>{$body}</p>"
            . "									</td>"
            . "								</tr>"
            . "							</table>"
            . "						</td>"
            . "					</tr>"
            . "					<tr>"
            . "						<td style='padding:30px;background:#5D005B;'>"
            . "							<table role='presentation' style='width:100%;border-collapse:collapse;border:0;border-spacing:0;font-size:9px;font-family:Arial,sans-serif;'>"
            . "								<tr>"
            . "									<td style='padding:0;width:50%;' align='left'>"
            . "										<p style='margin:0;font-size:14px;line-height:16px;font-family:Arial,sans-serif;color:#ffffff;'>"
            . "											&reg; Ticketapp, 2021<br/>"
            . "										</p>"
            . "									</td>"
            . "									<td style='padding:0;width:50%;' align='right'>"
            . "										<table role='presentation' style='border-collapse:collapse;border:0;border-spacing:0;'>"
            . "											<tr>"
            . "												<td style='padding:0 0 0 10px;width:38px;'>"
            . "													<a href='http://www.twitter.com/' style='color:#ffffff;'><img src='https://assets.codepen.io/210284/tw_1.png' alt='Twitter' width='38' style='height:auto;display:block;border:0;' /></a>"
            . "												</td>"
            . "												<td style='padding:0 0 0 10px;width:38px;'>"
            . "													<a href='http://www.facebook.com/' style='color:#ffffff;'><img src='https://assets.codepen.io/210284/fb_1.png' alt='Facebook' width='38' style='height:auto;display:block;border:0;' /></a>"
            . "												</td>"
            . "											</tr>"
            . "										</table>"
            . "									</td>"
            . "								</tr>"
            . "							</table>"
            . "						</td>"
            . "					</tr>"
            . "				</table>"
            . "			</td>"
            . "		</tr>"
            . "	</table>"
            . "</body>"
            . "</html>";*/

            $html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
            <html xmlns='http://www.w3.org/1999/xhtml' xml:lang='es' lang='es'>
            <head>
                <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <meta http-equiv='X-UA-Compatible' content='ie-edge'>
            </head>
            <body style='margin: 0 auto;'>
                <table width='100%' align='center' border='0' cellpadding='0' cellspacing='0' style='width:100%;max-width:600px;'>
                    <!-- header -->
                    <tr>
                        <td align='center' bgcolor='#560357' style='padding-top:40px;padding-bottom:40px;background: -moz-linear-gradient(top, #7E1984 0%, #560357 100%);background: -webkit-linear-gradient(top, #7E1984 0%, #560357 100%);background: linear-gradient(to bottom, #7E1984 0%, #560357 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#7E1984', endColorstr='#560357',GradientType=1 );'>
                            <a href='".URL_BASE."' target='_blank' border='0' style='text-decoration:none'>
                                <img src='".URL_BASE."assets/images/mail/ticketapp_logo@2x.png' alt='Logo Ticketapp' border='0' width='208' style='display:block;border:0;width: 208px;height:auto;'>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style='padding-top:5px;padding-bottom:5px' bgcolor='#7E1984'></td>
                    </tr>
                    <!-- Main -->
                    <tr>
                        <td>
                            <table width='100%' align='center' border='0' cellpadding='0' cellspacing='0' bgcolor='#560357'>
                                <tr>
                                    <td colspan='2' style='padding-top:30px; padding-right: 10px; padding-left: 10px; font-family: Arial, sans-serif; font-size: 25px; color: #ffffff; text-align: left; line-height:26px;'>{$titulo}</td>
                                </tr>
                                <tr>
                                    <td width='50%' style='width: 50%;max-width: 50%;padding-top:10px; padding-bottom:20px; padding-left: 10px; padding-right: 10px; font-family: Arial, sans-serif; font-size: 14px; color: #ffffff; text-align: left; line-height:26px;'>                                                                                       
                                            {$body}
                                    </td>
                                    <td width='50%' style='width: 50%;max-width: 50%;'><img src='".URL_BASE."assets/images/mail/yey@2x.png' alt='Logo Ticketapp' border='0' width='100%' style='display:block;border:0;width: 100%;height:auto;'></td>
                                </tr>
                                
                            </table>
                        </td>
                    </tr>
                    <!-- Dirección -->
            
                    <!-- footer -->
                    <tr>
                        <td>
                            <table width='100%' align='center' border='0' cellpadding='0' cellspacing='0' bgcolor='#ffffff' style='padding-top: 20px;font-family: Arial, sans-serif;color:#560357'>
                            <tr>
                                <td style='text-align: center;font-size: 14px;' colspan='3'>Síguenos en nuestras Redes sociales ♥</td>
                            </tr>
                            <tr>
                                <td width='33%' style='width: 33%;'></td>
                                <td width='33%' style='width: 33%;'>
                                    <table width='50%' align='center' border='0' cellpadding='0' cellspacing='0' style='padding-top: 20px;'>
                                        <tr>
                                            <td>
                                                <a href='https://www.facebook.com/ticketappmx/' target='_blank' border='0' style='text-decoration:none'>
                                                    <img src='".URL_BASE."assets/images/mail/Facebook@2x.png' alt='Facebook de Ticketapp' border='0' width='32' style='display:block;border:0;width: 32px;height:auto;'>
                                                </a>
                                            </td>
                                            <td>
                                                <a href='https://www.instagram.com/ticketappmx/' target='_blank' border='0' style='text-decoration:none'>
                                                    <img src='".URL_BASE."assets/images/mail/Instagram@2x.png' alt='Instagram de Ticketapp' border='0' width='32' style='display:block;border:0;width: 32px;height:auto;'>
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td width='33%' style='width: 33%;'></td>
                            </tr>
                            <tr>
                                <td style='text-align: center;font-size: 14px;padding-top: 20px' colspan='3'>
                                        ticketapp.live  es una plataforma que acerca a productores de eventos con asistentes  por lo que NO es responsable de la producción y logística del evento. <br>  
                                        Visita:  <a target='_blank' href='".URL_BASE."document-pdf/AvisoDePrivacidadTicketApp.live.pdf'>Aviso de privacidad </a> y <a target='_blank' href='".URL_BASE."document-pdf/TerminosyCondicionesTicketApp.live.pdf'>Términos y condiciones</a>
										
										
                                </td>
                            </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
            </html>";

        return $html;
    }
    public function sendWhatsapp($params)
    {        
        try {            
            $config_notification = parse_ini_file(__DIR__ . '../../../config/config_notification.ini');
            if ($config_notification["mode"] == 1) {
                $client = new Client($config_notification["SID"], $config_notification["Token"]);                

                    $message = $client->messages->create(
                        "whatsapp:+521{$params['cellPhone']}", // to
                           [
                               "from" => "whatsapp:{$config_notification["whatsAppNumber"]}",
                               "body" => $params["message"]
                           ]
                  );

                return $message->sid;
            }
        } catch (Exception $e) {            
            return $e->getMessage();
        }
    }

    public function sendSMS($params){
        try {
            $config_notification = parse_ini_file(__DIR__ . '../../../config/config_notification.ini');
            if ($config_notification["mode"] == 1) {
                $client = new Client($config_notification["SID"], $config_notification["token"]);
                $client->messages->create(
                    // the number you'd like to send the message to
                    "+521{$config_notification["smsNumber"]}",
                    [
                        // A Twilio phone number you purchased at twilio.com/console
                        'from' => $params['cellPhone'],
                        // the body of the text message you'd like to send
                        'body' => $params["message"]
                    ]
                );
            }
        } catch (Exception $e) {
        }
    }
}
