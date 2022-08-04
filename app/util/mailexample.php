<?php
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
        "key":"xLauaiDPNIB67XWubTd0QQ",
        "message":{
            "html": "Correo",
            "subject":"Subject",
            "from_email":"soporte@fullpassticket.app",
            "from_name":"fullpass",
            "to":[{
                "email":"arturomv1930@gmail.com"	
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
var_dump($dboxResponse);
curl_close($ch);
$sent = 1;